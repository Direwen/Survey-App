<template>
    <PageComponent title="Dashboard">
        <div v-if="loading" class="flex justify-center">Loading...</div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 text-gray-700">
            <!-- TOTAL SURVEYS -->
            <div class="bg-white shadow-md p-3 text-center flex flex-col order-1 lg:order-2 animate-fade-in-down"
                style="animation-delay: 0.1s;">
                <h3 class="text-2xl font-semibold">Total Surveys</h3>
                <div class="text-8xl font-semibold flex-1 flex items-center justify-center">{{ data.totalSurveys }}</div>
            </div>
            <!-- TOTAL ANSWERS -->
            <div class="bg-white shadow-md p-3 text-center flex flex-col order-2 lg:order-4 animate-fade-in-down"
                style="animation-delay: 0.3s;">
                <h3 class="text-2xl font-semibold">Total Answers</h3>
                <div class="text-8xl font-semibold flex-1 flex items-center justify-center">{{ data.totalAnswers }}</div>
            </div>
            <!-- LATEST SURVEY -->
            <div class="row-span-2 bg-white shadow-md p-4 order-3 lg:order-1 animate-fade-in-down"
                style="animation-delay: 0s;">
                <img :src="data.latestSurvey.image_url ? data.latestSurvey.image_url : 'https://cdn.vectorstock.com/i/preview-1x/65/30/default-image-icon-missing-picture-page-vector-40546530.jpg'" alt="image" class="w-[240px] mx-auto">
                <h3 class="font-bold text-xl mb-3">{{ data.latestSurvey.title }}</h3>
                <div class="flex justify-between text-sm mb-1">
                    <div>Create Date: </div>
                    <div>{{ data.latestSurvey.created_at }}</div>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <div>Expire Date: </div>
                    <div>{{ data.latestSurvey.expire_date ? data.latestSurvey.expire_date : '-' }}</div>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <div>Status: </div>
                    <div>{{ data.latestSurvey.status ? 'Active' : 'Draft' }}</div>
                </div>
                <div class="flex justify-between text-sm mb-1">
                    <div>Questions: </div>
                    <div>{{ data.latestSurvey.questions }}</div>
                </div>
                <div class="flex justify-between text-sm mb-3">
                    <div>Answers: </div>
                    <div>{{ data.latestSurvey.answers }}</div>
                </div>
                <div class="flex justify-between">
                    <router-link :to="{ name: 'SurveyView', params: { id: data.latestSurvey.id } }"
                        class="flex py-2 px-4 border border-transparent text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit Answer
                    </router-link>
                    <button
                        class="flex py-2 px-4 border border-transparent text-sm rounded-md text-indigo-500 hover:bg-indigo-700 hover:text-white transition-colors focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>

                        View Answers
                    </button>
                </div>
            </div>
            <!-- ANSWERS -->
            <div class="bg-white shadow-md p-3 row-span-2 order-4 lg:order-3 animate-fade-in-down"
                style="animation-delay: 0.2s;">
                <div class="flex justify-between items-center mb-3 px-2">
                    <h3 class="text-2xl font-semibold">Latest Answer</h3>
                    <a href="" class="text-sm text-blue-500 hover:decoration-blue-500">
                        View All
                    </a>
                </div>
                <a href="#" v-for="answer of data.latestAnswers" :key="answer.id" class="block p-2 hover:bg-gray-100/90">
                    <div class="font-semibold">{{ answer.survey.title }}</div>
                    <small>
                        Answer Made at:
                        <i class="font-semibold">{{ answer.end_date }}</i>
                    </small>
                </a>
            </div>
        </div>
    </PageComponent>
</template>

<script setup>
import { computed } from "vue";
import PageComponent from "../components/PageComponent.vue";
import store from "../store";

const loading = computed(() => store.state.dashboard.loading);
const data = computed(() => store.state.dashboard.data);

store.dispatch("getDashboardData");
</script>

<style></style>
