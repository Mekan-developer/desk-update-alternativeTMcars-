export const clickOutside = {
    mounted(el, binding) {
        el.__clickOutsideHandler__ = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                binding.value?.(event)
            }
        }
        document.addEventListener('click', el.__clickOutsideHandler__, true)
    },
    unmounted(el) {
        document.removeEventListener('click', el.__clickOutsideHandler__, true)
    },
}
