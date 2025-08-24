<script setup>
import { Head, usePage, useForm, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
const showPassword = ref(false);
import Loader from '@/Components/Loader/Loader.vue';

const loading = ref(false);
const message = usePage();
const form = useForm({
    password: '',
    password_confirmation: '',
});

function setNewPass() {
    loading.value = true;
    form.post(route('new.password'), {
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
            if (errors.password) {
                errorToast(errors.password);
            } else if (errors.password_confirmation) {
                errorToast(errors.password_confirmation)
            } else {
                errorToast('Password change failed');
            }
        }
    });
}
</script>

<template>

    <Head>
        <title>POS || Set New Password</title>
    </Head>

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">
        <div class="card shadow border-0 w-100" style="max-width: 600px;">
            <div class="p-5">
                <div class="text-center mb-4">
                    <h3 class="text-dark">Set New Password</h3>
                    <p class="text-muted">Create a strong password for your account.</p>
                </div>
                <form class="user" @submit.prevent="setNewPass">
                    <div class="form-group mb-3">
                        <input type="password" class="form-control form-control-user" id="password"
                            v-model="form.password" placeholder="Enter new password"
                            :class="{ 'is-invalid': form.errors.password }" />
                        <span v-if="form.errors.password" class="text-danger small">{{ form.errors.password }}</span>
                    </div>
                    <div class="form-group mb-3">
                        <input type="password" class="form-control form-control-user" id="password_confirmation"
                            v-model="form.password_confirmation" placeholder="Confirm password"
                            :class="{ 'is-invalid': form.errors.password_confirmation }" />
                        <span v-if="form.errors.password_confirmation" class="text-danger small">{{
                            form.errors.password_confirmation }}</span>
                    </div>

                    <button type="submit" class="btn btn-info btn-block w-100">Update</button>
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
