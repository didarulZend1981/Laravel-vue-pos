<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '@/Components/Loader/Loader.vue';
import { ref } from 'vue';

const message = usePage();
const loading = ref(false);

const form = useForm({
    email: '',
});

function sendOTP() {
    loading.value = true;
    form.post(route('send.OTP'), {
        onSuccess: () => {
            loading.value = false;
            if (message.props.flash.status === true) {
                successToast(message.props.flash.message);
                form.reset();
            } else {
                errorToast(message.props.flash.message);
            }
        },
        onError: (errors) => {
            loading.value = false;
            if (errors.email) {
                errorToast(errors.email);
            }
        }
    });
}
</script>

<template>

    <Head>
        <title>POS || Send OTP</title>
    </Head>

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow border-0 w-100" style="max-width: 600px;">
            <div class="p-5">
                <div class="text-center mb-4">
                    <h3 class="text-dark">Get OTP</h3>
                    <p class="text-muted">Enter your email address to receive an OTP.</p>
                </div>
                <form class="user" @submit.prevent="sendOTP" method="POST">
                    <div class="form-group mb-3">
                        <input type="email" v-model="form.email" placeholder="Enter email address"
                            class="form-control form-control-user" :class="{ 'is-invalid': form.errors.email }" />
                        <span v-if="form.errors.email" class="text-danger small">{{ form.errors.email }}</span>
                    </div>
                    <button type="submit" class="btn btn-info btn-block w-100">Send</button>
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
