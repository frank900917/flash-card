<template>
    <div v-if="flashCardSet" class="container-lg py-5">
        <div class="d-flex row items-center justify-between">
            <h1 class="mb-4 me-auto col-lg">{{ flashCardSet.title }}</h1>
            <div class="ms-auto col-xl-3 col-lg-4 align-self-center d-flex justify-content-lg-end">
                <NuxtLink :to="{ name:'flashCard-edit-id', params: { id: id } }"
                v-if="user?.id === flashCardSet.user_id"
                class="btn btn-primary m-2 align-self-center">
                    編輯
                </NuxtLink>
                <button type="button" class="btn btn-danger m-2 align-self-center" @click="handleDelate" :disabled="isSubmitting">
                    刪除
                </button>
                <NuxtLink to="/account" v-if="user" class="btn btn-success m-2 align-self-center">
                    返回帳戶
                </NuxtLink>
            </div>
        </div>
        <span class="align-self-center badge" :class="[flashCardSet.isPublic ? 'bg-primary' : 'bg-success']">
            {{ flashCardSet.isPublic ? "公開" : "私人" }}單字集
        </span>
        <p class="text-gray-600 my-3">{{ flashCardSet?.description }}</p>
        <h2 class="my-3 pb-2 border-bottom">單字列表</h2>
        <div class="space-y-4 mt-6">
            <div
                v-for="(detail, index) in flashCardSet.details"
                :key="index"
                class="d-flex items-center my-2 border rounded-3">
                <div class="col-sm-1 text-center font-semibold py-3 ">{{ index + 1 }}</div>
                <div class="col-sm-4 p-3 border-start">
                    <div class="text-gray-800">{{ detail.word }}</div>
                </div>
                <div class="col-sm-7 p-3 border-start">
                    <div class="text-gray-600">{{ detail.word_description }}</div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="error?.statusCode === 404" class="container-lg py-5">
        <p class="border rounded p-5 row justify-content-center fs-5 text-danger">此單字集不存在</p>
    </div>
</template>

<script setup>    
    const route = useRoute();
    const id = route.params.id;
    const isSubmitting = ref(false);
    const user = useSanctumUser();
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;

    // 取得單字集資料
    const { data: flashCardSet, error} = await useSanctumFetch(`${apiBase}/flashCard/${id}`);

    async function handleDelate() {
        try {
            const confirmed = confirm('確定要刪除此單字集嗎？')
            if (!confirmed) return
            isSubmitting.value = true;
            await $fetch(csrfURL, {
                credentials: 'include'
            });
            await $fetch(`${apiBase}/flashCard/${id}`, {
                method: 'DELETE',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                }
            });
            navigateTo('/account');
        } catch (error) {
            alert(error);
            isSubmitting.value = false;
        }
    }
</script>