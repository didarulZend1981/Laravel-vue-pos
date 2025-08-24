<script setup>
import { Head, Link, usePage, useForm } from '@inertiajs/vue3';
import Loader from '@/Components/Loader/Loader.vue';
import { ref } from 'vue';

const msg = usePage();
const loading = ref(false);

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    password: '',
    password_confirmation: '',
    address: '',
    image: null,
});

function signUp() {
    loading.value = true;
    form.post(route('signup'), {
        onSuccess: () => {
            loading.value = false;
            if (msg.props.flash.message) {
                successToast(msg.props.flash.message || 'Registration Successful');
                form.reset();
            }
        },
        onError: () => {
            loading.value = false;
            errorToast(msg.props.flash.message || 'Registration Failed');
        },
    });
}

</script>


<template>

    <Head>
        <title>POS || Sign Up</title>
    </Head>

    <div class="container py-5">
        <div class="card o-hidden border-0 shadow-lg bg-white">
            <div class="card-body p-0">
                <div class="row flex-column flex-lg-row">
                    <!-- Left Side: Welcome Message -->
                    <div
                        class="col-lg-5 d-none d-lg-flex bg-info align-items-center justify-content-center text-white p-4">
                        <div class="text-center">
                            <h3>Welcome to Point of Selling</h3>
                            <p class="mt-3">Manage your sales efficiently with our POS system. Sign up to get started!
                            </p>
                        </div>
                    </div>

                    <!-- Right Side: Form -->
                    <div class="col-lg-7 col-12 p-5">
                        <form class="user" @submit.prevent="signUp()">
                            <div class="row">
                                <div class="col-12 col-sm-6 mb-3">
                                    <input type="text" class="form-control" v-model="form.first_name"
                                        placeholder="Enter first name">
                                    <span v-if="form.errors.first_name" class="text-danger">{{ form.errors.first_name
                                        }}</span>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <input type="text" class="form-control" v-model="form.last_name"
                                        placeholder="Enter last name">
                                    <span v-if="form.errors.last_name" class="text-danger">{{ form.errors.last_name
                                        }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <input type="email" class="form-control" v-model="form.email"
                                    placeholder="Enter email address">
                                <span v-if="form.errors.email" class="text-danger">{{ form.errors.email }}</span>
                            </div>

                            <div class="mb-3">
                                <input type="tel" class="form-control" v-model="form.mobile"
                                    placeholder="Enter phone number">
                                <span v-if="form.errors.mobile" class="text-danger">{{ form.errors.mobile }}</span>
                            </div>

                            <div class="mb-3">
                                <textarea class="form-control" v-model="form.address" placeholder="Enter your address"
                                    rows="2"></textarea>
                                <span v-if="form.errors.address" class="text-danger">{{ form.errors.address }}</span>
                            </div>

                            <div class="row">
                                <div class="col-12 col-sm-6 mb-3">
                                    <input type="password" class="form-control" v-model="form.password"
                                        placeholder="Password">
                                    <span v-if="form.errors.password" class="text-danger">{{ form.errors.password
                                        }}</span>
                                </div>
                                <div class="col-12 col-sm-6 mb-3">
                                    <input type="password" class="form-control" v-model="form.password_confirmation"
                                        placeholder="Confirm Password">
                                    <span v-if="form.errors.password_confirmation" class="text-danger">{{
                                        form.errors.password_confirmation }}</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="image" class="form-label">Profile Image</label>
                                <input type="file" id="image" class="form-control"
                                    @change="(e) => form.image = e.target.files[0]">
                                <span v-if="form.errors.image" class="text-danger">{{ form.errors.image }}</span>
                            </div>

                            <button type="submit" class="btn btn-info w-100">Register Account</button>
                        </form>

                        <div class="text-center mt-3">
                            <Link class="small" :href="route('signIn.page')">Already have an account? Login!</Link>
                        </div>

                        <hr>

                        <div class="text-center">
                            <Link class="btn btn-sm btn-outline-danger" :href="route('comeing.soon')">Login with Google</Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />
</template>

<style scoped>
@media (max-width: 991px) {
    .d-none.d-lg-flex {
        display: none !important;
    }
}
</style>
