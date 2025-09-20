<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Loader from '@/Components/Loader/Loader.vue';

const message = usePage();
const showPassword = ref(false);
const loading = ref(false);

const form = useForm({
    email: '',
    password: '',
});

function signIn() {
    loading.value = true;
    form.post('/login', {
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
            } else if (errors.password) {
                errorToast(errors.password);
            } else {
                errorToast('Login failed!');
            }
        }
    });
}
</script>

<template>
    <Head>
        <title>POS || Sign In</title>
    </Head>

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow border-0 w-100" style="max-width: 500px;">
            <div class="p-5">
                <div class="text-center mb-4">
                    <h3 class="text-dark">Sign In</h3>
                    <p class="text-muted">Enter your credentials to continue</p>
                </div>

                <form @submit.prevent="signIn">
                    <div class="form-group mb-3">
                        <input
                            type="email"
                            class="form-control"
                            v-model="form.email"
                            placeholder="Enter email"
                            :class="{ 'is-invalid': form.errors.email }"
                        />
                        <span v-if="form.errors.email" class="text-danger small">{{ form.errors.email }}</span>
                    </div>

                    <div class="form-group mb-3 position-relative">
                        <input
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control"
                            v-model="form.password"
                            placeholder="Enter password"
                            :class="{ 'is-invalid': form.errors.password }"
                        />
                        <button type="button"
                            class="btn position-absolute end-0 top-50 translate-middle-y me-2"
                            @click="showPassword = !showPassword"
                            style="background: none; border: none;">
                            <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                        <span v-if="form.errors.password" class="text-danger small">{{ form.errors.password }}</span>
                    </div>

                    <button type="submit" class="btn btn-info w-100">Sign In</button>
                </form>

                <!-- <div class="text-center mt-3">
                    <Link class="small" :href="route('OTP.page')">Forgot Password?</Link>
                </div>
                <div class="text-center">
                    <Link class="small" :href="route('signup.page')">Don't have an account? Sign up!</Link>
                </div>
                <hr>
                <div class="text-center">
                    <Link class="btn btn-sm btn-outline-danger" :href="route('comeing.soon')">Login with Google</Link>
                </div> -->
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>

<style scoped>
.position-relative {
    position: relative;
}

.end-0 {
    right: 0;
}

.top-50 {
    top: 50%;
}

.translate-middle-y {
    transform: translateY(-50%);
}
</style>
