<template>
    <div class="container-lg py-5">
        <div class="d-flex items-center justify-between">
            <h1 class="mb-4">編輯單字集</h1>
            <button
                @click="navigateTo(`/flashCard/${id}`)"
                class="btn btn-success align-self-center ms-auto mx-2">
            返回單字集
            </button>
        </div>
        <FlashCardForm v-if="flashCardSet" :form="flashCardSet" :onSubmit="handleSubmit" :errors="errors"/>
    </div>
</template>

<script setup>
    definePageMeta({
        middleware: ['sanctum:auth']
    });

    const route = useRoute()
    const errors = ref({});
    const id = route.params.id;
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;
    
    // 取得單字集資料
    const { data: flashCardSet } = await useSanctumFetch(`${apiBase}/flashCard/${id}`);

    async function handleSubmit() {
        if (flashCardSet.value.details.length === 0) {
            alert('至少需要包含一個單字');
            return;
        }
        await $fetch(csrfURL, {
            credentials: 'include'
        });
        errors.value = {};
        try {
            await $fetch(`${apiBase}/flashCard/${id}`, {
                method: 'PUT',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                },
                body: flashCardSet.value
            });
            alert('更新成功！');
            navigateTo(`/flashCard/${id}`);
        } catch (error) {
            const backendErrors = error.response?._data?.errors;
            if (backendErrors) {
                for (const key in backendErrors) {
                    errors.value[key] = backendErrors[key][0];
                }
            } else {
                alert(error);
            }
        }
    }
</script>