<template>
    <PageComponent>
        <!-- HEADER SLOT -->
        <template v-slot:header>
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">Surveys</h1>
                <router-link
                    :to="{name: 'SurveyCreate'}"
                    class="py-2 px-3 text-white bg-emerald-500 rounded-md flex justify-between items-center hover:bg-emerald-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 align-middle">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                    Add New Survey
                </router-link>
            </div>
        </template>
        <!-- DEFAULT SLOT -->
        <div v-if="surveys.loading" class="flex justify-center">Loading ...</div>
        <div v-else>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3">
                <SurveyListItem
                    v-for="(survey, index) in surveys.data"
                    :key="survey.id"
                    :survey="survey"
                    class="opacity-0 animate-fade-in-down"
                    :style="{animationDelay: `${index * 0.3 }s`}"
                    @delete="deleteSurvey(survey)"
                />
            </div>
            <!-- Pagination Nav Bar -->
            <div class="flex justify-center mt-5">
                <nav 
                    class="relative z-0 inline-flex justify-center rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <a
                        v-for="(link, index) of surveys.links"
                        :key="index"
                        :disabled="!link.url"
                        v-html="link.label"
                        href="#"
                        @click="getForPage($event, link)"
                        aria-current="page"
                        class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
                        :class="[link.active ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50', index === 0 ? 'rounded-l-md' : '', index === surveys.links.length - 1 ? 'rounded-r-md' : '']" 
                    >
                    </a>
                </nav>
            </div>
        </div>  
    </PageComponent>
</template>

<script setup>
import store from "../store";
import PageComponent from "../components/PageComponent.vue";
import SurveyListItem from "../components/SurveyListItem.vue";
import { computed, onMounted } from "vue";

const surveys = computed(() => store.state.surveys);
//functions
function deleteSurvey(survey){
    if(confirm("Are you sure you want to delete this survey")){
        store.dispatch("deleteSurvey", survey.id)
            .then(() => {
                store.dispatch("getSurveys");
            })
    }
}
function getForPage(event, link){
    //Since user clicked a tag, this will prevent from redirecting which is the default behavior
    event.preventDefault();
    // if link url doesn't exist or link is already active, no works
    if(!link.url || link.active){
        return;
    }
    store.dispatch("getSurveys", {url: link.url});
}
//Lifehooks
onMounted(() => {
    store.dispatch('getSurveys')
})
</script>

<style></style>
