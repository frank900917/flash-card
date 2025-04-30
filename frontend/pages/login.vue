<template>
    <div class="d-flex justify-content-center align-items-center vh-100 bg-body-secondary">
        <div class="card shadow p-4" style="min-width: 350px; max-width: 400px;">
            <h3 class="text-center mb-4 fw-bolder">登入</h3>
            <form @submit.prevent="handleLogin">
                <div class="mb-3">
                    <label for="username" class="form-label">使用者名稱</label>
                    <input type="text" class="form-control" id="username" v-model="username" required autocomplete="off"
                        placeholder="輸入使用者名稱" :class="{ 'is-invalid': errors.username }" />
                    <div class="invalid-feedback">{{ errors.username }}</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼</label>
                    <input type="password" class="form-control" id="password" v-model="password" required 
                        placeholder="輸入密碼" :class="{ 'is-invalid': errors.password }" />
                    <div class="invalid-feedback">{{ errors.password }}</div>
                </div>

                <button type="submit" class="btn btn-primary w-100">登入</button>

                <div class="text-center mt-3">
                <NuxtLink to="/register" class="btn btn-outline-secondary w-100">還沒有帳號？註冊</NuxtLink>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
    const router = useRouter();
    const username = ref('');
    const password = ref('');
    const errors = ref({});
    const handleLogin = async () => {        
        const { apiBase } = useRuntimeConfig().public;
        const { csrfURL } = useRuntimeConfig().public;
        const user = useState('user', () => null);
        errors.value = {};

        await $fetch(csrfURL, {
            credentials: 'include'
        });
        try {
            const response = await $fetch(`${apiBase}/login`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'X-XSRF-TOKEN': useCookie('XSRF-TOKEN').value
                },
                body: {
                    username: username.value,
                    password: password.value
                }
            });
            user.value = response.user;
            router.push('/account');
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
