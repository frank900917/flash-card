<template>
    <form @submit.prevent="onSubmit">
        <div class="mb-3">
            <label class="form-label">標題</label>
            <input v-model="form.title" type="text" class="form-control" :class="{ 'is-invalid': errors.title }" required />
            <div class="invalid-feedback">{{ errors.title }}</div>
        </div>

        <div class="mb-3">
            <label class="form-label">說明</label>
            <textarea v-model="form.description" class="form-control" :class="{ 'is-invalid': errors.description }"></textarea>
            <div class="invalid-feedback">{{ errors.description }}</div>
        </div>

        <div class="form-check form-switch mb-4">
            <input v-model="form.isPublic" class="form-check-input" type="checkbox" id="publicSwitch">
            <label class="form-check-label" for="publicSwitch">公開此單字集</label>
        </div>

        <h2 class="mb-3">單字列表</h2>

        <div v-for="(detail, index) in form.details" :key="index" class="card mb-3 p-3">
            <div class="row align-items-center g-2">
                <div class="col-sm-1 d-flex justify-content-center align-items-center">
                    <span class="fw-bold">{{ index + 1 }}</span>
                </div>
                <div class="col-sm d-flex flex-column" style="min-width: 150px;">
                    <label class="form-label text-center">單字</label>
                    <input v-model="detail.word" type="text" class="form-control" :class="{ 'is-invalid': errors[`details.${index}.word`] }" required>
                    <div class="invalid-feedback">{{ errors[`details.${index}.word`] }}</div>
                </div>
                <div class="col-sm d-flex flex-column" style="min-width: 150px;">
                    <label class="form-label text-center">說明</label>
                    <input v-model="detail.word_description" type="text" class="form-control" :class="{ 'is-invalid': errors[`details.${index}.word_description`] }" required>
                    <div class="invalid-feedback">{{ errors[`details.${index}.word_description`] }}</div>
                </div>
                <div class="col-lg-1 col-sm-auto d-flex align-items-center">
                    <button type="button" class="btn btn-danger w-100" @click="removeDetail(index)">刪除</button>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mb-4">
            <button type="button" class="btn btn-outline-success" @click="addDetail">
            新增單字
            </button>
        </div>

        <div class="d-flex justify-content-center">
            <button type="submit" class="btn btn-primary">保存</button>
        </div>
    </form>
</template>

<script setup>
    const { form, errors, onSubmit } = defineProps({
        form: Object,
        errors: Object,
        onSubmit: Function
    })

    // 新增單字欄
    function addDetail () {
        form.details.push({ word: '', word_description: '' });
    };

    // 刪除單字欄
    function removeDetail (index) {
        form.details.splice(index, 1);
    };
</script>