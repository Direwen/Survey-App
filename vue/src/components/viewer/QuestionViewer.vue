<template>
  <fieldset class="mb-4">
    <div>
      <legend class="text-base font-medium text-gray-900">
        {{ index + 1 }}. {{ question.question }}
      </legend>
    </div>
    <div class="mt-3">
      <!-- SELECT QUESTION TYPE -->
      <div v-if="question.type === 'select'">
        <select
          :value="modelValue"
          @change="emits('update:modelValue', $event.target.value)"
          class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        >
          <option value="">Please Select</option>
          <option 
            v-for="option in question.data.options"
            :key="option.uuid"
            :value="option.text"
          >
            {{ option.text }}
          </option>
        </select>
      </div>
      <!-- RADIO QUESTION TYPE -->
      <div v-else-if="question.type === 'radio'">
        <div
          v-for="option of question.data.options"
          :key="option.uuid"
          class="flex items-center"
        >
          <input 
            :id="option.uuid"
            :name="'question' + question.id"
            :value="option.text"
            @change="emits('update:modelValue', $event.target.value)"
            type="radio"
            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300"
          >
          <label 
            :for="option.uuid"
            class="ml-3 block text-sm font-medium text-gray-700"
          >
            {{ option.text }}
          </label>
        </div>
      </div>
      <!-- CHECKBOX QUESTION TYPE -->
      <div v-else-if="question.type === 'checkbox'">
        <div
          v-for="option of question.data.options"
          :key="option.uuid"
          class="flex items-center"
        >
          <input 
            type="checkbox"
            v-model="model[option.text]"
            @change="onCheckboxChange"
            :id="option.uuid"
            class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
          >
          <label 
            :for="option.uuid"
            class="ml-3 block text-sm font-medium text-gray-700"
          >
            {{ option.text }}
          </label>
        </div>
      </div>
      <!-- TEXT QUESTION TYPE -->
      <div v-else-if="question.type === 'text'">
        <input 
          type="text"
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
        >
      </div>
      <!-- TEXTAREA QUESTION TYPE -->
      <div v-else-if="question.type === 'textarea'">
        <textarea
          :value="modelValue"
          @input="emits('update:modelValue', $event.target.value)"
          class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
        ></textarea>
      </div>
    </div>
  </fieldset>

  <hr class="mb-4">
</template>

<script setup>
import { ref } from "vue";

const {question, index, modelValue} = defineProps({
  question: Object,
  index: Number,
  modelValue: [String, Array],//A prop named modelValue, which the local ref's value is synced with
});
const emits = defineEmits(["update:modelValue"]);//update:modelValue, which is emitted when the local ref's value is mutated

let model;
if(question.type === "checkbox"){
  model = ref({});
}

//Function
function onCheckboxChange(){
  // {
  //   Burger : true,  
  // }
  // Burger will be checkBoxOptionKey in this case
  const selectedOptions = [];
  for(let checkBoxOptionKey in model.value){
    //if the value is true, push
    if(model.value[checkBoxOptionKey]){
      selectedOptions.push(checkBoxOptionKey);
    }
  }
  emits("update:modelValue", selectedOptions);
}
</script>

<style>

</style>