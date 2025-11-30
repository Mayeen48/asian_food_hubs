// useToast.js (global singleton)
import { ref } from "vue"

const visible = ref(false)
const message = ref("")
const type = ref("success")

export function useToast() {
  function showToast(msg, t = "info") {
    message.value = msg
    type.value = t
    visible.value = true

    setTimeout(() => {
      visible.value = false
    }, 2500)
  }

  return {
    visible,
    message,
    type,
    showToast
  }
}
