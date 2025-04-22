<template>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <NuxtLink class="navbar-brand fw-bolder" to="/">線上單字卡</NuxtLink>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <NuxtLink class="nav-link" to="/">首頁</NuxtLink>
                </div>
                <div class="navbar-nav ms-auto">
                    <ClientOnly>
                        <template v-if="user">
                            <NuxtLink class="nav-link" to="/account">{{ user.username }}</NuxtLink>
                            <button class="btn btn-sm btn-secondary mx-2" @click="logout">登出</button>
                        </template>
                        <template v-else>
                            <NuxtLink to="/register" class="btn btn-outline-secondary mx-2">註冊</NuxtLink>
                            <NuxtLink to="/login" class="btn btn-secondary mx-2">登入</NuxtLink>                        
                        </template>
                    </ClientOnly>
                </div>
            </div>
        </div>
    </nav>
    <slot />
</template>

<script setup>
    const router = useRouter()
    const { user, fetchUser } = useAuth()
    const { apiBase } = useRuntimeConfig().public

    onMounted(() => {
        fetchUser()
    })

    const logout = async () => {
        await $fetch(`${apiBase}/logout`, {
            method: 'POST',
            credentials: 'include',
            headers: {
                'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
            }
        })
        user.value = null
        await router.push('/')
    }
</script>