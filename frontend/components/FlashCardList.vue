<template>
    <NuxtLink :to="{ name:'flashCard-id', params: { id: flashCardSet.id } }"
    class="d-flex mb-3 text-decoration-none text-dark border border-2 rounded-3 row">
        <div class="col-lg-8 col-sm-6 p-3">
            <div class="fw-bold text-truncate">{{ flashCardSet.title }}</div>
            <div class="text-muted text-truncate">{{ flashCardSet.description }}</div>
        </div>
        <div class="col-lg-2 col-sm-3 col-6 p-3 text-center card-info">
            <div>{{ flashCardSet.details_count }} 個單字</div>
            <div>{{ formatDate(flashCardSet.updated_at) }}</div>
        </div>
        <div class="col-lg-2 col-sm-3 col-6 p-3 text-center border-start card-info">
            <div class="text-truncate">作者：{{ flashCardSet.author }}</div>
            <div class="align-self-center badge" :class="[flashCardSet.isPublic ? 'bg-primary' : 'bg-success']">
                {{ flashCardSet.isPublic ? '公開' : '私人' }}單字集
            </div>
        </div>
    </NuxtLink>
</template>

<script setup>
    const { flashCardSet } = defineProps(['flashCardSet'])

    // 格式化時間
    function formatDate(dateStr) {
        const date = new Date(dateStr)
        return date.toLocaleDateString()
    }

    // 隱藏作者中間部分
    // function maskedAuthor(author) {
    //     return author.slice(0, 3) + '***' + author.slice(-3)
    // }
</script>

<style scoped>
.card-info {
    border-top: 1px solid var(--bs-border-color);
    border-left: 0;
}

@media (min-width: 576px) {
    .card-info {
        border-top: 0;
        border-left: 1px solid var(--bs-border-color);
    }
}
</style>