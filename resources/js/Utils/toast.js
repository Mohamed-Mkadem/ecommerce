
import { POSITION } from "vue-toastification";

export function getToastOptions() {

    const currentDirection = document.documentElement.getAttribute('dir'); // Default to 'en'
    const isRTL = currentDirection == 'rtl'
    return {
        // position: POSITION.TOP_RIGHT, // Toast position
        rtl: isRTL, // Enable RTL for Arabic
        transition: "Vue-Toastification__fade", // Default transition
        maxToasts: 5, // Maximum toasts
        newestOnTop: true, // Newest toast on top
    };
}
