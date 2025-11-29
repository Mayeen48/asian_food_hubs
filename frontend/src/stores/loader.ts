import { defineStore } from "pinia";
import { ref } from "vue";

export const useLoaderStore = defineStore("loader", () => {
  const loading = ref(false);
  const count = ref(0); // for multiple requests

  function show() {
    count.value++;
    loading.value = true;
  }

  function hide() {
    count.value--;
    if (count.value <= 0) {
      loading.value = false;
      count.value = 0;
    }
  }

  return { loading, show, hide };
});
