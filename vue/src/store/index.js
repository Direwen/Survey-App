import { createStore } from "vuex";
import axiosClient from "../axios";

const store = createStore({
  state: {
    user: {
      data: {},
      token: sessionStorage.getItem("TOKEN"),
    },
    currentSurvey: {
      loading: false,
      data: {}
    },
    surveys: {
      loading: false,
      links: [],
      data: []
    },
    questionTypes: ["text", "select", "radio", "checkbox", "textarea"],
    notification: {
      show: false,
      type: null,
      message: null,
    },
    dashboard: {
      loading: false,
      data: {}
    }
  },
  getters: {},
  mutations: {
    dashboardLoading(state, loading){
      state.dashboard.loading = loading;
    },
    setDashboardData(state, data){
      state.dashboard.data = data;
    },
    setCurrentSurveyLoading(state, loading){
      state.currentSurvey.loading = loading;
    },
    setSurveysLoading(state, loading){
      state.surveys.loading = loading;
    },
    setCurrentSurvey(state, survey){
      state.currentSurvey.data = survey.data;
    },
    logout(state) {
      //reset user data to default
      state.user.data = {};
      state.user.token = null;
      //delete the stored token from session storage
      sessionStorage.removeItem("TOKEN");
    },
    setUser(state, userData) {
      //save user data
      state.user.token = userData.token;
      state.user.data = userData.user;
      //store token in a session to avoid from deleting all data when refreshed
      sessionStorage.setItem("TOKEN", userData.token);
    },
    setSurveys(state, surveys){
      console.log(surveys);
      state.surveys.links = surveys.meta.links;
      state.surveys.data = surveys.data;
    },
    notify(state, {message, type}){
      state.notification.show = true;
      state.notification.type = type;
      state.notification.message = message;
      setTimeout(() => {
        state.notification.show = false;
        console.log("After 3 seconds", state.notification.show);
      }, 3000)
    }
    
  },
  actions: {
    getSurvey({commit}, id){
      commit("setCurrentSurveyLoading", true);
      return axiosClient.get(`/survey/${id}`)
        .then(response => {
          console.log(response.data);
          commit("setCurrentSurvey", response.data);
          commit("setCurrentSurveyLoading", false);
          return response;
        })
        .catch(err => {
          commit("setCurrentSurveyLoading", false);
          throw err;
        })
    },
    register({ commit }, user) {
      return axiosClient.post('/register', user)
        .then(({data}) => {
          commit('setUser', data);
          return data;
        })
        .catch((error) => {
          console.log(typeof(error));
          return Promise.reject(error);
        });
    },
    login({ commit }, user) {
      return axiosClient.post('/login', user)
        .then(({data}) => {
          commit('setUser', data);
          return data;
        })
        .catch(err => {
          console.log("Error arrived => " + err);
          throw err;
        });
    },
    logout({commit}){
      return axiosClient.post('/logout')
        .then(response => {
          commit('logout');
          return response;
        })
    },
    saveSurvey({commit}, survey){
      console.log(survey);
      //delete image_url which is only meant to be used for display purposes
      delete survey.image_url;
      let response;
      //if the survey has an ID, Update the survey
      //if not, create a new survey
      if(survey.id){
        response = axiosClient.put(`/survey/${survey.id}`, survey)
          .then(response => {
            console.log(response);
            commit("setCurrentSurvey", response.data);
            return response.data;
          })
          .catch(err => {
            console.log(err);
            throw err;
          })
      }else{
        response = axiosClient.post("/survey", survey)
          .then(response => {
            console.log(response);
            commit("setCurrentSurvey", response.data);
            return response.data;
          })
          .catch(err => {
            console.log(err);
            throw err;
          })
      }
      return response;
    },
    deleteSurvey({}, id){
      return axiosClient.delete(`survey/${id}`);
    },
    getSurveys({commit}, {url = null} = {}){
      // getSurveys({}): url will be null
      // getSurveys(): url will be null
      url = url || '/survey';
      commit("setSurveysLoading", true);
      return axiosClient.get(url)
        .then(response => {
          commit("setSurveysLoading", false);
          commit("setSurveys", response.data);
          return response;
        })
    },
    getSurveyBySlug({commit}, slug){
      commit("setCurrentSurveyLoading", true);
      return axiosClient.get(`/survey-by-slug/${slug}`)
        .then(response => {
          console.log("Arrived", response);
          commit("setCurrentSurvey", response.data);
          commit("setCurrentSurveyLoading", false);
          return response;
        })
        .catch(error => {
          commit("setCurrentSurveyLoading", false);
          throw error;
        })
    },
    saveSurveyAnswer({commit}, {surveyId, answers}){
      return axiosClient.post(`/survey/${surveyId}/answer`, {answers});
    },
    getDashboardData({commit}){
      commit('dashboardLoading', true);
      console.log("Loading On");
      return axiosClient.get(`/dashboard`)
        .then(response => {
          commit('dashboardLoading', false);
          commit('setDashboardData', response.data);
          return response;
        })
        .catch(error => {
          commit('dashboardLoading', false);
          return error;
        })
    }
  },
  modules: {},
});

export default store;
