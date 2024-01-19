<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSurveyAnswerRequest;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveyAnswer;
use App\Http\Requests\StoreSurveyRequest;
use App\Http\Requests\UpdateSurveyRequest;
use App\Http\Resources\SurveyResource;
use App\Models\SurveyQuestionAnswer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

//php artisan make:controller SurveyController --model=Survey --api --requests
class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //get the current user from the request
        $user = $request->user();
        //get all surveys of this current user
        $surveys = Survey::where('user_id', $user->id)->paginate(3);
        return SurveyResource::collection($surveys);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSurveyRequest $request)
    {
        //returns the validated data from the incoming request and then create
        $data = $request->validated();
        //Check if image was given, must be saved on local file system
        if(isset($data['image'])){
            $relativePath = $this->saveImage($data['image']);
            $data['image'] = $relativePath;
        }
        $survey = Survey::create($data);

        //Crate new questions
        foreach($data['questions'] as $question){
            $question['survey_id'] = $survey->id;//adding survey id to each question
            $this->createQuestion($question);//will create a question record in DB
        }
        // to define how the Survey model should be presented when returned as a JSON response
        return new SurveyResource($survey);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurveyRequest $request, Survey $survey)
    {
        //Get the validated data from the request
        $data = $request->validated();
        //Check if the image was given
        if(isset($data['image'])){
            //Save the image in the storage
            $relativePath = $this->saveImage($data['image']);
            //Add Image path to data
            $data['image'] = $relativePath;
            //if there's an old image, delete it
            if($survey->image){
                $absolutePath = public_path($survey->image);
                File::delete($absolutePath);
            }
        }
        //Update the survey
        $survey->update($data);

        //Get Id of old questions from $survey e.g. [1 => 4, 2 => 5]
        //will extract id from a collection of questions in an array format
        $existingIds = $survey->questions()->pluck('id')->toArray();
        //Get Id of new questions from $data e.g. [1 => 4, 2 => '333-333', 3 => '4324-543-14234']
        //will extract id from data['questions'] array
        $newIds = Arr::pluck($data['questions'], 'id');
        //Find questions to delete e.g. [5] will be deleted
        $toDelete = array_diff($existingIds, $newIds);
        //Find Questions to add e.g. ['333-333', '4324-543-14234'] will be added
        $toAdd = array_diff($newIds, $existingIds);
        //Delete questions
        SurveyQuestion::destroy($toDelete);
        //Create new Questions
        foreach($data["questions"] as $question){
            // if the question id of request is in toAdd array, create a new question  
            if(in_array($question['id'], $toAdd)){
                $question['survey_id'] = $survey->id;
                $this->createQuestion($question);
            }
        }
        //Update existing questions
        // keys the collection by the given key
        //['333-333' => {id: '333-333',...}]
        $questionMap = collect($data['questions'])->keyBy('id');
        foreach($survey->questions as $question){
            if(isset($questionMap[$question->id])){
                $this->updateQuestion($question, $questionMap[$question->id]);
            }
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource.
     */
    public function show(Survey $survey, Request $request)
    {
        //check whether the current user has a permission to access this survey
        $user = $request->user();
        if($user->id !== $survey->user_id){
            abort(403, "Unauthorized Action");
        }
        return new SurveyResource($survey);
    }

    /**
     * Display the specified resource for Guest.
     */
    public function showForGuest(Survey $survey)
    {
        return new SurveyResource($survey);
    }

    public function storeAnswer(StoreSurveyAnswerRequest $request, Survey $survey, )
    {
        $validated = $request->validated();
        $surveyAnswer = SurveyAnswer::create([
            'survey_id' => $survey->id,
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s'),
        ]);
        
        foreach($validated['answers'] as $questionId => $answer){
            // CHECK WHETHER THE QUESTIONS ARE REALLY ON THIS SURVEY
            $question = SurveyQuestion::where(['id' => $questionId, 'survey_id' => $survey->id])->get();
            //if the question doesn't exist
            if(!$question){
                return response("Invalid Question ID: $questionId", 400);
            }
            //Create survey_question_answer record
            $data = [
                'survey_question_id' => $questionId,
                'survey_answer_id' => $surveyAnswer->id,
                'answer' => is_array($answer) ? json_encode($answer) : $answer
            ];
            SurveyQuestionAnswer::create($data);

            return response("", 201);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Survey $survey, Request $request)
    {
        //check whether the current user has a permission to access this survey
        $user = $request->user();
        if($user->id !== $survey->user_id){
            abort(403, "Unauthorized Action");
        }
        //Delete the survey
        $survey->delete();
        //if there's an old image, delete it
        if($survey->image){
            $absolutePath = public_path($survey->image);
            File::delete($absolutePath);
        }
        return response('', 204);
    }

    //========= PRIVATE FUNCTIONS ===============
    private function saveImage($image)
    {
        // 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAU5ErkJggg==';
        //'/^data:image\/(\w+);base64,/'
        //third parameter $type will have an array of 2 items
        //1st item is the full match : data:image/png;base64
        //2nd item is the (\w+) which will capture this "png", format of the image 

        // Step 1: Check if the image is in a data URI format (data:image/)
        if(preg_match('/^data:image\/(\w+);base64,/', $image, $type)){
            //Take out the base64 encoded text without mime type : "iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAU5ErkJggg=="
            $image = substr($image, strpos($image, ',') + 1);
            //Get File extension
            $type = strtolower($type[1]);//jpg, png, gif
            //Check if file is an image
            if(!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])){
                throw new Exception("Invalid Image Type");
            }
            // Step 3: Decode the base64 image data
            $image = str_replace(' ', '+', $image);
            //Decodes the base64-encoded image data into binary
            $image = base64_decode($image);
            // Step 4: Check if base64 decoding was successful
            if($image === false){
                throw new Exception('base64_decode failed');
            }
        }else{
            throw new Exception('Did not match data URI with image data');
        }
        // Step 5: Define the directory and file path for saving the image
        $dir = 'images/';
        $file = Str::random() . '.' . $type;
        $absolutePath = public_path($dir);
        $relativePath = $dir . $file;
        // Step 6: Check if the directory exists, if not, create it
        if(!File::exists($absolutePath)){
            //creates it with appropriate permissions
            File::makeDirectory($absolutePath, 0755, true);
        }
        // Step 7: Save the image to the specified file path
        file_put_contents($relativePath, $image);
        // Step 8: Return the relative path of the saved image
        return $relativePath;
    }

    private function createQuestion($data)
    {
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);//turn this array into JSON to store
        }
        $validator = Validator::make($data, [
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,

            ])],
            'description' => 'nullable|string',
            'data' => 'present',
            'survey_id' => 'exists:surveys,id',//'exists:App\Models\Survey,id' 
        ]);

        return SurveyQuestion::create($validator->validated());
    }

    private function updateQuestion(SurveyQuestion $question, $data)
    {
        if(is_array($data['data'])){
            $data['data'] = json_encode($data['data']);
        }
        $validator = Validator::make($data, [
            'id' => 'exists:survey_questions,id',//'exists:App\Models\SurveyQuestion,id'
            'question' => 'required|string',
            'type' => ['required', Rule::in([
                Survey::TYPE_TEXT,
                Survey::TYPE_TEXTAREA,
                Survey::TYPE_SELECT,
                Survey::TYPE_RADIO,
                Survey::TYPE_CHECKBOX,
            ])],
            'description' => 'nullable|string',
            'data' => 'present'
        ]);

        return $question->update($validator->validated());
    }
}


