import { ref } from 'vue'

const visible = ref(false)
const message = ref('')
const type = ref<'success' | 'error'>('success')

export function useToast() {
  function showToast(msg: string, toastType: 'success' | 'error' = 'success') {
    message.value = msg
    type.value = toastType
    visible.value = true

    setTimeout(() => {
      visible.value = false
    }, 3000)
  }

  return {
    visible,
    message,
    type,
    showToast,
  }
}
