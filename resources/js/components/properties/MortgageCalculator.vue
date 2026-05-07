<script setup>
import { computed, reactive, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    propertyPrice: { type: Number, default: 0 },
});

const { t, locale } = useI18n({ useScope: 'global' });

const state = reactive({
    price: normalizeNumber(props.propertyPrice),
    downPayment: normalizeNumber(props.propertyPrice) * 0.2,
    downPct: 20,
    propertyTax: 0,
    hoaFees: 0,
    years: 30,
    interestRate: 4.125,
});

watch(
    () => props.propertyPrice,
    (value) => {
        const nextPrice = normalizeNumber(value);
        state.price = nextPrice;
        state.downPayment = nextPrice * (state.downPct / 100);
    },
);

const principal = computed(() => Math.max(state.price - state.downPayment, 0));
const principalInterest = computed(() => {
    const months = Math.max(1, normalizeNumber(state.years) * 12);
    const rate = normalizeNumber(state.interestRate) / 100 / 12;

    if (principal.value <= 0) return 0;
    if (rate <= 0) return principal.value / months;

    const payment = (principal.value * rate * Math.pow(1 + rate, months)) / (Math.pow(1 + rate, months) - 1);
    return Number.isFinite(payment) ? payment : 0;
});
const taxMonthly = computed(() => normalizeNumber(state.propertyTax) / 12);
const hoaMonthly = computed(() => normalizeNumber(state.hoaFees) / 12);
const monthlyPayment = computed(() => principalInterest.value + taxMonthly.value + hoaMonthly.value);
const donutStyle = computed(() => {
    const total = monthlyPayment.value;

    if (total <= 0) {
        return {
            background: 'conic-gradient(#2A559C 0deg 120deg, #5BA3E8 120deg 240deg, #DC2626 240deg 360deg)',
        };
    }

    const principalDegrees = (principalInterest.value / total) * 360;
    const taxDegrees = principalDegrees + (taxMonthly.value / total) * 360;

    return {
        background: `conic-gradient(#2A559C 0deg ${principalDegrees}deg, #5BA3E8 ${principalDegrees}deg ${taxDegrees}deg, #DC2626 ${taxDegrees}deg 360deg)`,
    };
});

function normalizeNumber(value) {
    const number = Number(value);
    return Number.isFinite(number) && number > 0 ? number : 0;
}

function formatCurrency(value) {
    const number = Math.round(normalizeNumber(value) * 100) / 100;
    const formatted = new Intl.NumberFormat(locale.value === 'ar' ? 'ar-AE' : 'en-AE', { maximumFractionDigits: 2 }).format(number);
    return `${formatted} ${locale.value === 'ar' ? 'د.إ' : 'AED'}`;
}

function syncDownPaymentFromAmount() {
    state.downPayment = Math.min(Math.max(normalizeNumber(state.downPayment), 0), normalizeNumber(state.price));
    state.downPct = state.price > 0 ? Math.round((state.downPayment / state.price) * 10000) / 100 : 0;
}

function syncDownPaymentFromPct() {
    state.downPct = Math.min(Math.max(normalizeNumber(state.downPct), 0), 100);
    state.downPayment = Math.round(normalizeNumber(state.price) * (state.downPct / 100));
}
</script>

<template>
    <div v-if="state.price > 0" class="property-detail-calc-outer">
        <section class="property-detail-section property-detail-section--flush" aria-labelledby="pd-calc-heading">
            <div class="property-detail-calc">
                <p id="pd-calc-heading" class="property-detail-calc__heading">{{ t('propertyDetail.calcTitle') }}</p>
                <div class="property-detail-calc__layout">
                    <div class="property-detail-calc__donut-wrap">
                        <div class="property-detail-calc__donut" role="img" :aria-label="t('propertyDetail.paymentChartAria')" :style="donutStyle">
                            <div class="property-detail-calc__donut-label">
                                <p class="property-detail-calc__donut-value">{{ formatCurrency(monthlyPayment) }}</p>
                                <p class="property-detail-calc__donut-sub">{{ t('propertyDetail.perMonth') }}</p>
                            </div>
                        </div>
                        <ul class="property-detail-calc__legend">
                            <li>
                                <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--pi" aria-hidden="true"></span> {{ t('propertyDetail.pi') }}</span>
                                <span>{{ formatCurrency(principalInterest) }}</span>
                            </li>
                            <li>
                                <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--tax" aria-hidden="true"></span> {{ t('propertyDetail.tax') }}</span>
                                <span>{{ formatCurrency(taxMonthly) }}</span>
                            </li>
                            <li>
                                <span class="property-detail-calc__legend-label"><span class="property-detail-calc__dot property-detail-calc__dot--hoa" aria-hidden="true"></span> {{ t('propertyDetail.hoa') }}</span>
                                <span>{{ formatCurrency(hoaMonthly) }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="property-detail-calc__panel">
                        <div class="property-detail-calc__fields property-detail-calc__fields--grid">
                            <div class="property-detail-calc__field">
                                <label for="calc_price">{{ t('propertyDetail.priceLabel') }}</label>
                                <input id="calc_price" v-model.number="state.price" name="calc_price" type="number" inputmode="decimal" min="0" step="100">
                            </div>
                            <div class="property-detail-calc__field property-detail-calc__field--down">
                                <label for="calc_down_pct">{{ t('propertyDetail.downPaymentLabel') }}</label>
                                <div class="property-detail-calc__down-row">
                                    <input id="calc_down_amount" v-model.number="state.downPayment" name="calc_down_amount" type="number" inputmode="decimal" min="0" step="100" aria-describedby="calc_down_pct-hint" @input="syncDownPaymentFromAmount">
                                    <span class="property-detail-calc__down-sep" aria-hidden="true"></span>
                                    <div class="property-detail-calc__pct-wrap">
                                        <input id="calc_down_pct" v-model.number="state.downPct" name="calc_down_pct" type="number" inputmode="decimal" min="0" max="100" step="0.01" @input="syncDownPaymentFromPct">
                                        <span class="property-detail-calc__pct-suffix">%</span>
                                    </div>
                                </div>
                                <span id="calc_down_pct-hint" class="visually-hidden">{{ t('propertyDetail.downPaymentHint') }}</span>
                            </div>
                            <div class="property-detail-calc__field property-detail-calc__field--readonly">
                                <span class="property-detail-calc__readonly-label">{{ t('propertyDetail.pi') }}</span>
                                <p class="property-detail-calc__readonly-value">{{ formatCurrency(principalInterest) }}</p>
                            </div>
                            <div class="property-detail-calc__field">
                                <label for="calc_tax">{{ t('propertyDetail.taxLabel') }}</label>
                                <input id="calc_tax" v-model.number="state.propertyTax" name="calc_tax" type="number" inputmode="decimal" min="0" step="100">
                            </div>
                            <div class="property-detail-calc__field">
                                <label for="calc_hoa">{{ t('propertyDetail.hoaLabel') }}</label>
                                <input id="calc_hoa" v-model.number="state.hoaFees" name="calc_hoa" type="number" inputmode="decimal" min="0" step="100">
                            </div>
                            <div class="property-detail-calc__field">
                                <label for="calc_years">{{ t('propertyDetail.termLabel') }} <span class="property-detail-calc__hint">{{ t('propertyDetail.termHint') }}</span></label>
                                <input id="calc_years" v-model.number="state.years" name="calc_years" type="number" inputmode="numeric" min="1" max="50">
                            </div>
                            <div class="property-detail-calc__field">
                                <label for="calc_rate">{{ t('propertyDetail.interestLabel') }}</label>
                                <input id="calc_rate" v-model.number="state.interestRate" name="calc_rate" type="number" inputmode="decimal" min="0" step="0.001">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
