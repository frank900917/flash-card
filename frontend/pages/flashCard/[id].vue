<template>
    <div class="container-lg py-5">
        <div class="d-flex items-center justify-between">
            <h1 class="mb-4">{{ flashCardSet.title }}</h1>
            <div class="ms-auto">
                <NuxtLink to="/account" v-if="user" class="btn btn-success align-self-center mx-2">
                    返回帳戶
                </NuxtLink>
                <NuxtLink :to="{ name:'flashCard-edit-id', params: { id: id } }"
                v-if="user?.id === flashCardSet.user_id"
                class="btn btn-primary align-self-center mx-2">
                    編輯
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
</template>

<script setup>
    const route = useRoute();
    const router = useRouter();
    const id = route.params.id;
    const flashCardSet = ref({
        title: '',
        description: '',
        public: false,
        details: [
            { word: '', word_description: '' }
        ]
    });
    const { user, fetchUser } = useAuth();
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;

    onMounted(async () => {
        try {
            // 取得單字集資料
            await $fetch(csrfURL, {
                credentials: 'include'
            });
            const data = await $fetch(`${apiBase}/flashCard/${id}`, {
                method: 'GET',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                }
            });
            flashCardSet.value = data;
            // 取得目前登入的使用者
            fetchUser(); // 假設你的API是這樣
        } catch (error) {
            console.error(error);
            // 失敗就跳回列表
            router.push('/');
        }
    });
</script>