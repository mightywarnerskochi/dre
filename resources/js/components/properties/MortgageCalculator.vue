<script setup>
import { computed, reactive, watch } from 'vue';

const props = defineProps({
    propertyPrice: { type: Number, default: 1000000 },
});

const state = reactive({
    price: props.propertyPrice,
    downPayment: props.propertyPrice * 0.2,
    interestRate: 3.5,
    tenure: 25,
});

watch(
    () => props.propertyPrice,
    (value) => {
        const next = Number(value || 0);
        state.price = next;
        state.downPayment = Math.min(state.downPayment, next) || next * 0.2;
    },
);

const monthlyPayment = computed(() => {
    const principal = Math.max(state.price - state.downPayment, 0);
    const rate = state.interestRate / 100 / 12;
    const months = state.tenure * 12;

    if (!months) return 0;
    if (rate === 0) return principal / months;

    const payment = (principal * rate * Math.pow(1 + rate, months)) / (Math.pow(1 + rate, months) - 1);

    return Number.isFinite(payment) ? Math.round(payment) : 0;
});

function formatPrice(num) {
    return new Intl.NumberFormat('en-AE').format(Number(num || 0));
}
</script>

<template>
    <div class="dre-mortgage">
        <div class="dre-mortgage__head">
            <h3>Payment Estimate</h3>
            <p>Adjust the values to get a quick mortgage snapshot.</p>
        </div>

        <div class="dre-mortgage__result">
            <span>Monthly payment</span>
            <strong>{{ formatPrice(monthlyPayment) }} AED</strong>
        </div>

        <div class="dre-mortgage__grid">
            <label class="dre-mortgage__field">
                <span>Property Price</span>
                <input v-model.number="state.price" type="number" min="0">
            </label>

            <label class="dre-mortgage__field">
                <span>Down Payment</span>
                <input v-model.number="state.downPayment" type="number" min="0">
            </label>

            <label class="dre-mortgage__field">
                <span>Interest Rate</span>
                <input v-model.number="state.interestRate" type="number" min="0" step="0.1">
            </label>

            <label class="dre-mortgage__field">
                <span>Loan Term</span>
                <input v-model.number="state.tenure" type="number" min="1">
            </label>
        </div>
    </div>
</template>

<style scoped>
.dre-mortgage {
    background: #fff;
    border: 1px solid #e3eaf5;
    border-radius: 26px;
    padding: 24px;
    box-shadow: 0 18px 38px rgba(17, 38, 70, 0.06);
}

.dre-mortgage__head {
    margin-bottom: 16px;
}

.dre-mortgage__head h3 {
    margin: 0 0 6px;
    font-size: 20px;
    color: #12284c;
}

.dre-mortgage__head p {
    margin: 0;
    color: #77849a;
    font-size: 14px;
    line-height: 1.6;
}

.dre-mortgage__result {
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-bottom: 18px;
    padding: 18px;
    border-radius: 18px;
    background: linear-gradient(180deg, #f7faff 0%, #eef4ff 100%);
    border: 1px solid #dee8fb;
}

.dre-mortgage__result span {
    color: #6f7d93;
    font-size: 13px;
}

.dre-mortgage__result strong {
    font-size: 30px;
    line-height: 1.1;
    color: #1f4eb4;
}

.dre-mortgage__grid {
    display: grid;
    gap: 12px;
}

.dre-mortgage__field {
    display: grid;
    gap: 6px;
}

.dre-mortgage__field span {
    color: #6f7d93;
    font-size: 13px;
    font-weight: 600;
}

.dre-mortgage__field input {
    width: 100%;
    min-height: 46px;
    padding: 0 14px;
    border: 1px solid #dde6f2;
    border-radius: 14px;
    background: #f9fbff;
    color: #10233f;
    outline: none;
}

.dre-mortgage__field input:focus {
    border-color: #2f58b7;
    box-shadow: 0 0 0 3px rgba(47, 88, 183, 0.12);
}

@media (max-width: 767px) {
    .dre-mortgage {
        padding: 18px;
        border-radius: 20px;
    }

    .dre-mortgage__result strong {
        font-size: 24px;
    }
}
</style>
