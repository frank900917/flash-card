<template>
    <div v-if="flashCardSet" class="container-lg py-5">
        <!-- 標題區 -->
        <div class="d-flex row items-center justify-between">
            <h1 class="mb-4 me-auto col-xl pe-0">{{ flashCardSet.title }}</h1>
            <div class="ms-auto col-xl-4 align-self-center d-flex justify-content-xl-end ps-0">
                <button v-if="quizState === 0" class="btn btn-info m-2" data-bs-toggle="modal" data-bs-target="#startQuizModal">測驗</button>
                <button v-if="quizState === 1" class="btn btn-outline-danger m-2" @click="quizState = 0">取消測驗</button>
                <button v-if="quizState === 2" class="btn btn-outline-info m-2" @click="quizState = 0">離開測驗</button>
                <NuxtLink :to="{ name:'flashCard-edit-id', params: { id: id } }"
                v-if="user?.id === flashCardSet.user_id"
                class="btn btn-primary m-2 align-self-center">
                    編輯
                </NuxtLink>
                <button v-if="user?.id === flashCardSet.user_id" type="button" 
                class="btn btn-danger m-2 align-self-center" :disabled="isSubmitting" data-bs-toggle="modal" data-bs-target="#confirmModal">
                    刪除
                </button>
                <NuxtLink to="/account" v-if="route.query.from === 'account'" class="btn btn-success m-2 align-self-center">
                    返回帳戶
                </NuxtLink>
                <NuxtLink to="/public" v-else class="btn btn-success m-2 align-self-center">
                    返回
                </NuxtLink>
            </div>
        </div>
        <!-- 單字集基本資料 -->
        <span class="align-self-center badge" :class="[flashCardSet.isPublic ? 'bg-primary' : 'bg-success']">
            {{ flashCardSet.isPublic ? "公開" : "私人" }}單字集
        </span>
        <p class="text-gray-600 my-3">{{ flashCardSet?.description }}</p>
        <!-- 顯示單字集 -->
        <div v-if="quizState === 0">
            <div class="d-flex my-3 pb-2 border-bottom">
                <h2 class="me-auto">單字列表</h2>
                <PaginationSizeSelector v-model="perPage" :onChange="fetchFlashCardSet"/>
            </div>
            <div class="space-y-4 mt-6">
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <div class="col-4 d-flex justify-content-center">單字</div>
                    <div class="col-7 d-flex justify-content-center">說明</div>
                </div>
                <div v-for="(detail, index) in flashCardSet.details.data" :key="index" class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index + flashCardSet.details.from }}</div>
                    <div class="col-4 p-3 border-start d-flex align-items-center">{{ detail.word }}</div>
                    <div class="col-7 p-3 border-start d-flex align-items-center">{{ detail.word_description }}</div>
                </div>
            </div>
            <PaginationControl v-model="page" :datas="flashCardSet.details" :fetchDatas="fetchFlashCardSet"/>
        </div>
        <!-- 單字集測驗表單 -->
        <form v-if="quizState === 1" @submit.prevent="handlefinishQuiz">
            <div class="d-flex my-3 pb-2 border-bottom">
                <h2 class="me-auto align-self-center mb-0">測驗</h2>
                <button type="submit" class="btn btn-info align-self-center">完成測驗</button>
            </div>
            <div class="space-y-4 mt-6" >
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <div class="col-4 d-flex justify-content-center">單字</div>
                    <div class="col-7 d-flex justify-content-center">說明</div>
                </div>
                <div v-for="(item, index) in quizData" :key="index" class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index + 1 }}</div>
                    <div class="col-4 p-3 border-start d-flex align-items-center">
                        <template v-if="quizType === 'word'">
                            <input v-model="item.answer" type="text" class="form-control" placeholder="輸入單字" />
                        </template>
                        <template v-else>
                            {{ item.word }}
                        </template>
                    </div>
                    <div class="col-7 p-3 border-start d-flex align-items-center">
                        <template v-if="quizType === 'description'">
                            <input v-model="item.answer" type="text" class="form-control" placeholder="輸入說明" />
                        </template>
                        <template v-else>
                            {{ item.word_description }}
                        </template>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="submit" class="btn btn-info m-2">完成測驗</button>
                </div>
            </div>
        </form>
        <!-- 單字集測驗結果 -->
        <div v-if="quizState === 2">
            <div class="d-flex my-3 pb-2 border-bottom">
                <h2 class="me-auto align-self-center mb-0">測驗結果</h2>
            </div>
            <div class="mt-3">
                <div class="fw-bold">答題數：{{ correctCount }}/{{ quizData.length }}</div>
                <div class="fw-bold">答題率：{{ accuracyRate }}%</div>
            </div>
            <div class="space-y-4 mt-6">
                <div class="d-flex mt-3 fw-bold">
                    <div class="col-1"></div>
                    <template v-if="quizType === 'word'">
                        <div class="col-2 d-flex justify-content-center">單字</div>
                        <div class="col-2 d-flex justify-content-center">你的回答</div>
                        <div class="col-7 d-flex justify-content-center">說明</div>
                    </template>
                    <template v-if="quizType === 'description'">
                        <div class="col-3 d-flex justify-content-center">單字</div>
                        <div class="col-4 d-flex justify-content-center">說明</div>
                        <div class="col-4 d-flex justify-content-center">你的回答</div>
                    </template>
                </div>
                <div v-for="(item, index) in quizData" :key="index" class="d-flex items-center my-2 border rounded-3">
                    <div class="col-1 p-3 d-flex align-items-center justify-content-center">{{ index + 1 }}</div>
                    <template v-if="quizType === 'word'">
                        <div class="col-2 p-3 border-start d-flex align-items-center">{{ item.word }}</div>
                        <div class="col-2 p-3 border-start d-flex align-items-center">
                            <div :class="item.isCorrect ? 'text-success' : 'text-danger'">{{ item.answer }}</div>
                        </div>
                        <div class="col-7 p-3 border-start d-flex align-items-center">{{ item.word_description }}</div>
                    </template>
                    <template v-if="quizType === 'description'">
                        <div class="col-3 p-3 border-start d-flex align-items-center">{{ item.word }}</div>
                        <div class="col-4 p-3 border-start d-flex align-items-center">{{ item.word_description }}</div>
                        <div class="col-4 p-3 border-start d-flex align-items-center">
                            <div :class="item.isCorrect ? 'text-success' : 'text-danger'">{{ item.answer }}</div>
                        </div>
                    </template>
                </div>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-outline-info m-2" @click="quizState = 0">離開測驗</button>
                </div>
            </div>
        </div>
    </div>
    <div v-if="error?.statusCode === 404" class="container-lg py-5">
        <p class="border rounded p-5 row justify-content-center fs-5 text-danger">此單字集不存在</p>
    </div>
    <!-- 刪除單字集確認視窗 -->
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">確認</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">確定要刪除此單字集？</div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-danger" @click="handleDelate">確定</button>
                </div>
            </div>
        </div>
    </div>
    <!-- 測驗設定視窗 -->
    <div class="modal fade" id="startQuizModal" tabindex="-1" aria-labelledby="startQuizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form id="startQuizForm" @submit.prevent="handleStartQuiz">
                    <div class="modal-header">
                        <h5 class="modal-title" id="startQuizModalLabel">開始測驗</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="quizType" class="form-label w-100">測驗類型</label>
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="quizType" id="wordCheckbox" value="word"
                                :class="{ 'is-invalid': errors.quizType }">
                                <label for="wordCheckbox">單字</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="quizType" id="descriptionCheckbox" value="description"
                                :class="{ 'is-invalid': errors.quizType }">
                                <label for="descriptionCheckbox">說明</label>
                            </div>
                            <div class="text-danger">{{ errors.quizType }}</div>
                        </div>
                        <div>
                            <label for="quizNumber" class="form-label">測驗單字數</label>
                            <input type="number" class="form-control" id="quizNumber" :placeholder="`需介於 1 至 ${flashCardSet.details.total} 之間`"
                            :class="{ 'is-invalid': errors.quizNumber }">
                            <div class="invalid-feedback">{{ errors.quizNumber }}</div>
                        </div>
                        <div class="text-danger">{{ errors.unique }}</div>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                        <button type="submit" class="btn btn-primary">確定</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>    
    const route = useRoute();
    const id = route.params.id;
    const isSubmitting = ref(false);
    const user = useSanctumUser();
    const page = ref(1);
    const perPage = ref(25);
    const quizState = ref(0);
    const quizData = ref([]);
    const quizType = ref('');
    const errors = ref({});
    const { $bootstrap } = useNuxtApp();
    const { apiBase } = useRuntimeConfig().public;
    const { csrfURL } = useRuntimeConfig().public;
    
    // 取得單字集資料
    const { data: flashCardSet , error } = await useSanctumFetch(`${apiBase}/flashCard/${id}?type=show&page=${page.value}&perPage=${perPage.value}`);
    if (error.value?.statusCode === 404) {
        throw createError({ statusCode: 404, statusMessage: '此單字集不存在' });
    } else if (error.value?.statusCode === 403) {
        throw createError({ statusCode: 403, statusMessage: '此單字集為私人單字集' });
    }
    
    // 換頁更新單字集清單
    async function fetchFlashCardSet(isPerPage) {
        if (isPerPage) {
            page.value = 1;
        }
        const data = await $fetch(`${apiBase}/flashCard/${id}?type=show&page=${page.value}&perPage=${perPage.value}`, {
            method: 'GET',
            credentials: 'include'
        });
        flashCardSet.value = data;
    }

    // 刪除單字集
    async function handleDelate() {
        const confirmModal = $bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
        confirmModal.hide();
        try {
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

    // 開始測驗
    async function handleStartQuiz() {
        quizType.value = document.querySelector('input[name="quizType"]:checked')?.value;
        const quizNumberInput = document.getElementById('quizNumber');
        const quizNumber = Number(quizNumberInput.value);

        errors.value = {};
        if (!quizType.value) {
            errors.value.quizType = '請選擇測驗類型';
            return false;
        }

        if (!Number.isInteger(quizNumber)) {
            errors.value.quizNumber = '請輸入有效的整數';
            return false;
        }

        const data = await $fetch(`${apiBase}/flashCard/${id}?type=quiz`, {
            method: 'GET',
            credentials: 'include'
        });

        if (quizNumber < 1 || quizNumber > data.length) {
            errors.value.quizNumber = `請輸入介於 1 至 ${data.length} 之間的整數`;
            return false;
        }

        if (quizType.value === 'description') {
            const unique = new Set();
            for (const item of data) {
                if (unique.has(item.word_description)) {
                    errors.value.unique = `此單字集含有重複的說明：${item.word_description}，無法進行說明測驗`;
                    console.log(item.word_description);
                    
                    return false;
                }
                unique.add(item.word_description);
            }
        }

        // 隨機排序抽取單字
        const shuffled = [...data].sort(() => 0.5 - Math.random());
        quizData.value = shuffled.slice(0, quizNumber);

        quizState.value = 1;

        const startQuizModal = $bootstrap.Modal.getInstance(document.getElementById('startQuizModal'));
        startQuizModal.hide();
    }

    // 結束測驗
    async function handlefinishQuiz() {
        quizData.value = quizData.value.map(item => {
            const answer = item.answer ? item.answer : '未作答';
            const correctAnswer = quizType.value === 'word' ? item.word : item.word_description;
            const isCorrect = item.answer?.trim().toLowerCase() === correctAnswer.toLowerCase();
            return {
                ...item,
                answer: answer,
                isCorrect: isCorrect
            }
        })
        quizState.value = 2;
    }

    // 計算答對數
    const correctCount = computed(() =>
        quizData.value.filter(item => item.isCorrect).length
    )

    // 計算正確率（四捨五入至小數點後兩位）
    const accuracyRate = computed(() =>
        quizData.value.length === 0 ? 0 : ((correctCount.value / quizData.value.length) * 100).toFixed(2)
    )
</script>