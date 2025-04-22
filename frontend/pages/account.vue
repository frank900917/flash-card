<template>
    <div class="container mt-5">
        <h2>帳戶資料</h2>
        <div class="card p-4 mt-3">
            <p v-if="user"><strong>已登入，帳號：{{ user?.username }}</strong></p>
        </div>
    </div>
</template>

<script setup>
    const user = useState('user')
    definePageMeta({
        middleware: 'auth'
    })
</script>