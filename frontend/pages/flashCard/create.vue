<template>
    <div class="container-lg py-5">
        <div class="d-flex">
            <h1 class="mb-4">建立單字集</h1>
            <NuxtLink to="/account" class="btn btn-success align-self-center ms-auto mx-2">返回帳戶</NuxtLink>
        </div>
        <FlashCardForm :form="form" :onSubmit="handleSubmit" :errors="errors"/>
    </div>
</template>

<script setup>
    const user = useState('user');
    const errors = ref({});
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;

    // 表單資料
    const form = ref({
        title: '',
        description: '',
        author: user.value.username,
        isPublic: false,
        details: [
            { word: '', word_description: '' }
        ]
    });

    async function handleSubmit() {
        if (form.value.details.length === 0) {
            alert('至少需要新增一個單字');
            return;
        }
        await $fetch(csrfURL, {
            credentials: 'include'
        });
        errors.value = {};
        try {
            await $fetch(`${apiBase}/flashCard`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                },
                body: form.value
            });
            alert('建立成功');
            navigateTo('/account');
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