export default defineNuxtRouteMiddleware(async () => {
    const { user, fetchUser } = useAuth()
    await fetchUser()
    if (!user.value) {
        return navigateTo('/')
    }
  })
  