export const useAuth = () => {
    const { apiBase } = useRuntimeConfig().public
    const user = useState('user')
    const fetchUser = async () => {
        if (user.value) return
        try {
            const response = await $fetch(`${apiBase}/user`, {
                credentials: 'include'
            })
            user.value = response
        } catch (e) {
            user.value = null
        }
    }
    return { user, fetchUser }
}