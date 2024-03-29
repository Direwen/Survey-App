<template>
  <PageComponent>
    <!-- HEADER SLOT -->
    <template v-slot:header>
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ route.params.id ? model.title : "Create a Survey" }}
            </h1>

            <button 
                type="button"
                class="py-2 px-3 text-white flex justify-between items-center gap-2 bg-red-500 rounded-md hover:bg-red-600"
                v-if="route.params.id"
                @click="deleteSurvey()"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                Delete Survey
            </button>
        </div>
    </template>
    <div v-if="surveyLoading">Loading ....</div>
    <!-- DEFAULT SLOT -->
    <form @submit.prevent="saveSurvey" class="animate-fade-in-down" v-else>
        <div class="shadow sm:rounded-md sm:overflow-hidden">
            <!--Start SURVEY FIELDS SECTION -->
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <!-- IMAGE -->
                <div>
                    <label for="" class="block text-sm font-medium text-gray-700">
                        Image
                    </label>
                    <div class="mt-1 flex items-center">
                        <img
                            v-if="model.image_url"
                            :src="model.image_url"
                            :alt="model.title"
                            class="w-64 h-48 object-cover"
                        >
                        <span 
                            v-else
                            class="flex justify-center items-center h-12 w-12 rounded-full overflow-hidden bg-gray-100"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-gray-300">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>

                        </span>
                        <button
                            type="button"
                            class="relative overflow-hidden ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            <input 
                                type="file"
                                @change="onImageChoose"
                                class="absolute left-0 top-0 right-0 bottom-0 opacity-0 cursor-pointer"
                            >
                            Change
                        </button>
                    </div>
                </div>
                <!-- TITLE -->
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">
                        Title
                    </label>
                    <input
                    type="text"
                    name="title"
                    id="title"
                    v-model="model.title"
                    autocomplete="survey_title"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                </div>

                <!-- Description -->
                <div>
                    <label for="about" class="block text-sm font-medium text-gray-700">
                        Description
                    </label>
                    <div class="mt-1">
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            v-model="model.description"
                            autocomplete="survey_description"
                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md"
                            placeholder="Describe your survey"
                        />
                    </div>
                </div>

                <!-- Expire Date -->
                <div>
                    <label
                    for="expire_date"
                    class="block text-sm font-medium text-gray-700"
                    >
                        Expire Date
                    </label>
                    <input
                    type="date"
                    name="expire_date"
                    id="expire_date"
                    v-model="model.expire_date"
                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                    />
                </div>

                <!-- Status -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input
                            id="status"
                            name="status"
                            type="checkbox"
                            v-model="model.status"
                            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                        />
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="status" class="font-medium text-gray-700"
                            >Active</label
                        >
                    </div>
                </div>
            </div>    
            <!--End SURVEY FIELDS SECTION -->

            <!-- Start Question Section -->
            <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                <h3 class="text-2xl font-semibold flex items-center justify-between">
                    Questions
                    <!-- Add New Question Button -->
                    <button
                        type="button"
                        @click="addQuestion()"
                        class="flex items-center text-sm py-1 px-4 rounded-sm text-white bg-gray-600 hover:bg-gray-700"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 align-middle">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Add Question
                    </button>
                </h3>
                <!-- Display Question Section -->
                <div v-if="!model.questions.length" class="text-center text-gray-600">
                    You Don't Have Any Questions Created
                </div>
                <div v-for="(question, index) in model.questions" :key="question.id">
                    <QuestionEditor 
                        :question="question" 
                        :index="index"
                        @change="questionChange"
                        @addQuestion="addQuestion"
                        @deleteQuestion="deleteQuestion"
                    />
                </div>
            </div>
            <!-- End Question Section -->

            <!--Start Button Section -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <button 
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Save
                </button>
            </div>
            <!--End Button Section -->

        </div>
    </form>
  </PageComponent>
</template>

<script setup>
import {v4 as uuidv4} from "uuid";
import { computed, onMounted, ref, watch } from 'vue';
import PageComponent from '../components/PageComponent.vue';
import QuestionEditor from '../components/editor/QuestionEditor.vue';
import { useRoute, useRouter } from 'vue-router';
import store from "../store";

const router = useRouter();
const route = useRoute();
const surveyLoading = computed(() => store.state.currentSurvey.loading);
//Create empty Survey
let model = ref({
    title: "",
    status: false,
    description: null,
    image: null,
    image_url: null,
    expire_date: null,
    questions: [],
})
//watch the current survey data change and when this happens, update local modle var
watch(() => store.state.currentSurvey.data, (newVal, oldVal) => {
    model.value = {
        ...JSON.parse(JSON.stringify(newVal)),
        status: newVal.status !== "draft",
    }
});
onMounted(() => {
    // get Survey data via the id which is in route params
    if(route.params.id){
        store.dispatch('getSurvey', route.params.id);
    }
})
//To Save Survey
function saveSurvey(){
    store.dispatch('saveSurvey', model.value)
        .then(({data}) => {
            store.commit('notify', {
                type: 'success',
                message: 'Survey was successfully updated'
            })
            console.log(store.state.notification.show);
            router.push({name: "SurveyView", params:{id: data.id}})
        })
        .catch(err => {
            console.log("ERROR : " + err);
        })
}
//To delte Survey
function deleteSurvey(){
    if(confirm("Are you sure you want to delete")){
        store.dispatch("deleteSurvey", model.value.id)
            .then(() => {
                router.push({name: "Surveys"})
            })
    }
}
//For Image Field
function onImageChoose(event){
    //This line retrieves the selected file from the input
    const file = event.target.files[0];
    //FileReader allows reading the contents of the selected file
    const reader = new FileReader();
    //onload event is triggered when the file reading operation is completed
    reader.onload = () => {
        //The Field to send on backend and apply validation
        //returns the data URL of the loaded file and will be base64-encoded
        model.value.image = reader.result;
        //The field to dispaly here
        model.value.image_url = reader.result;
    }
    //initiates the reading of the file
    reader.readAsDataURL(file);
}
//Emit Receiver Functions
function addQuestion(index){
    //This will accept the index of the new question
    const newQuestion = {
        id: uuidv4(),
        type: "text",
        question: "",
        description: null,
        data: {},
    };
    //1st param for the starting point, 2nd param for the number of items to remove, 3rd param to insert one
    model.value.questions.splice(index, 0, newQuestion);
}
function deleteQuestion(question){
    //to delete a specific question, filter functil will be used to get a new array
    model.value.questions = model.value.questions.filter(param => param !== question);
}
function questionChange(question){
    model.value.questions = model.value.questions.map(q => {
        // to replace the old question with a modified question
        if(q.id === question.id){
            return JSON.parse(JSON.stringify(question));//to avoid any intentional ref changes
        }
        //return the rests by default
        return q;
    });
}
</script>

<style>

</style>