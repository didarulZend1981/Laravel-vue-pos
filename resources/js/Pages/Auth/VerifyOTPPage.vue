<script setup>
import { Link, Head, useForm, usePage, } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import Loader from '@/Components/Loader/Loader.vue';

const message = usePage();
const countdownTime = ref(60);
const resendOtpVisible = ref(false);
const loading = ref(false);

function startCountdown() {
    resendOtpVisible.value = false; // Hide the resend link initially
    countdownTime.value = 60; // Reset the countdown

    const timer = setInterval(() => {
        if (countdownTime.value > 0) {
            countdownTime.value -= 1;
        } else {
            clearInterval(timer);
            resendOtpVisible.value = true;
        }
    }, 1000);
}

onMounted(() => {
    startCountdown();
});

//object declaration
const form = useForm({
    otp: '',
});

//otp verify
function verifyOtp() {
    loading.value = true;
    form.post(route('verify.OTP'), {
        onSuccess: () => {
            loading.value = false;
            if (message.props.flash.status === true) {
                successToast(message.props.flash.message);
                form.reset();
            } else {
                errorToast(message.props.flash.message);
            }
        }, onError: (errors) => {
            loading.value = false;
            if (errors.otp) {
                errorToast(errors.otp);
            }
        }
    });
}
</script>

<template>

    <Head>
        <title>POS || Verify OTP</title>
    </Head>

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow border-0 w-100" style="max-width: 600px;">
            <div class="p-5">
                <div class="text-center mb-4">
                    <h3 class="text-dark">Verify OTP</h3>
                    <p class="text-muted">Enter the 5-digit OTP sent to your email.</p>
                </div>
                <form class="user" @submit.prevent="verifyOtp" method="post">
                    <div class="form-group mb-3">
                        <input type="number" class="form-control form-control-user" id="otp" v-model="form.otp"
                            placeholder="Enter 5-digit OTP" :class="{ 'is-invalid': form.errors.otp }" />
                        <span v-if="form.errors.otp" class="text-danger small">{{ form.errors.otp }}</span>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-3 text-danger">
                        <span v-if="!resendOtpVisible">{{ countdownTime }} seconds remaining</span>
                        <Link v-if="resendOtpVisible" href="/otp">Resend OTP</Link>
                    </div>

                    <button type="submit" class="btn btn-info btn-block w-100">Verify</button>
                </form>
                <div class="text-center mt-3">
                    <Link class="small" :href="route('signIn.page')">Back to Sign In</Link>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>


<style scoped></style>
