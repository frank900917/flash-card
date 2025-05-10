<template>
    <div class="container py-5">
        <h1 class="mb-4">帳戶資料</h1>
        <div class="card p-4 mt-3">
            <p v-if="user" class="d-flex mb-0">
                <div>
                    <div class="fw-bold">帳號：{{ user.username }}</div>
                    <div class="fw-bold">註冊時間：{{ formatDate(user.created_at) }}</div>
                    <div class="fw-bold">已建立單字集：{{ FlashCardLists.total }}</div>
                </div>
                <NuxtLink to="/changePassword" class="btn btn-primary align-self-center mx-2 ms-auto">變更密碼</NuxtLink>
            </p>
        </div>
        <div class="d-flex">
            <h2 class="my-3 pb-2 me-auto">我的單字集</h2>
            <PaginationSizeSelector v-model="perPage" :onChange="fetchFlashCardLists"/>
            <NuxtLink to="/flashCard/create" class="btn btn-success align-self-center mx-2">建立新單字集</NuxtLink>
        </div>
        <div v-if="FlashCardLists.data.length > 0" class="space-y-4">
            <FlashCardList v-for="(set, index) in FlashCardLists.data" :key="index" :flashCardSet="set"/>
        </div>
        <div v-else class="border rounded p-5 row justify-content-center fs-5 text-danger">
            尚未建立任何單字集！
        </div>
        <PaginationControl v-model="page" :datas="FlashCardLists" :fetchDatas="fetchFlashCardLists"/>
    </div>
</template>

<script setup>
    definePageMeta({
        middleware: ['sanctum:auth']
    });

    const user = useSanctumUser();
    const page = ref(1);
    const perPage = ref(25);
    const { apiBase } = useRuntimeConfig().public;
    
    // 獲取帳戶單字集清單
    const { data: FlashCardLists} = await useSanctumFetch(`${apiBase}/flashCard?page=${page.value}&perPage=${perPage.value}`);
    
    // 格式化時間
    function formatDate(dateStr) {
        const date = new Date(dateStr)
        return date.toLocaleDateString()
    }
    // 更新單字集清單
    async function fetchFlashCardLists(isPerPage) {
        if (isPerPage) {
            page.value = 1;
        }
        const data = await $fetch(`${apiBase}/flashCard?page=${page.value}&perPage=${perPage.value}`, {
            method: 'GET',
            credentials: 'include'
        });
        FlashCardLists.value = data;
    }
</script>