<template>
    <div class="container mt-5">
        <h1 class="mb-4">帳戶資料</h1>
        <ClientOnly>
            <div class="card p-4 my-3">
                <p v-if="user"><strong>已登入，帳號：{{ user?.username }}</strong></p>
            </div>
        </ClientOnly>
        <div class="d-flex">
            <h2 class="my-3 pb-2">我的單字集</h2>
            <NuxtLink to="/flashCard/create" class="btn btn-success align-self-center ms-auto mx-2">建立新單字集</NuxtLink>
        </div>
        <div v-if="flashCardSets" class="space-y-4">
            <FlashCardList v-for="(set, index) in flashCardSets" :key="index" :flashCardSet="set"/>
        </div>
        <div v-else class="text-gray-500 text-center mt-10">
            尚未建立任何單字集！
        </div>
    </div>
    
</template>

<script setup>
    const user = useState('user');
    definePageMeta({
        middleware: 'auth'
    });

    const { apiBase } = useRuntimeConfig().public;

    const { data: flashCardSets } = await useFetch(`${apiBase}/flashCard`, {
        method: 'GET',
        credentials: 'include',
        server: false
    });
</script>