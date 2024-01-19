<template>
    <div class="py-5 px-8">
        <div v-if="loading" class="flex justify-center">Loading...</div>
        <form v-else class="container mx-auto" @submit.prevent="submitSurvey">
            <!-- Survey Image, Title & Desc -->
            <div class="grid grid-cols-6 items-center">
                <div class="mr-4">
                    <img :src="survey.image_url" alt="image">
                </div>
                <div class="col-span-5">
                    <h1 class="text-3xl mb-3">{{ survey.title }}</h1>
                    <p class="text-gray-500 text-sm" v-html="survey.description"></p>
                </div>
            </div>
            <div 
                v-if="surveyFinished"
                class="py-8 px-6 bg-emerald-400 text-white w-[600px] mx-auto"
            >
                <div class="text-xl mb-3 font-semibold">
                    Thank you for participating in this survey.
                </div>   
                <button
                    @click="submitAnotherResponse"
                    type="button"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Submit Another Response
                </button>
            </div>
            <!-- Survey Questions -->
            <div v-else>
                <hr class="my-3" />
                <!-- <pre>{{ answers}}</pre> -->
                <div
                    v-for="(question, index) of survey.questions"
                    :key="question.id"
                >
                    <!-- will pass v-model (modelValue) as answers[question.id] -->
                    <QuestionViewer 
                        v-model="answers[question.id]"
                        :question="question"
                        :index="index"
                    />
                </div>

                <button 
                    type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:rind-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Submit
                </button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import QuestionViewer from "../components/viewer/QuestionViewer.vue";
import store from "../store";

const route = useRoute();
const loading = computed(() => store.state.currentSurvey.loading);
const survey = computed(() => store.state.currentSurvey.data);
const surveyFinished = ref(false);
const answers = ref({});

//Submit function
function submitSurvey(){
    console.log(JSON.stringify(answers.value, undefined, 2));
    //save the answers
    store
        .dispatch("saveSurveyAnswer", {surveyId: survey.value.id, answers: answers.value})
        .then((response) => {
            if(response.status === 201){
                surveyFinished.value = true;
            }
        })
}
//To Submit another response function
function submitAnotherResponse(){
    //reset the form
    answers.value = {};
    surveyFinished.value = false;
}

onMounted(() => {
    store.dispatch("getSurveyBySlug", route.params.slug);
})
</script>

<style>

</style>