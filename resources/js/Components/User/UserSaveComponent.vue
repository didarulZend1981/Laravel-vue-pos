<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Loader from '../Loader/Loader.vue';
import { ref } from 'vue';

const page = usePage();
const loading = ref(false);
const showPassword = ref(false);

// Check if brand data exists
const user = page.props.user || {};

// Initialize form with existing data or defaults
const form = useForm({
    first_name: user.first_name || '',
    last_name: user.last_name || '',
    email: user.email || '',
    role: user.role || '2',
    mobile: user.mobile || '',
    password: user.password || '',
    password_confirmation: user.password_confirmation || '',
    address: user.address || '',
    image: user.image || '',

    // description: brand.description || '',
});


// Reactive property for dynamic image preview
const imagePreview = ref(
    user.image ? `/storage/user/${user?.image}` : '/asset/img/placeholder.jpg'
);

// Update image preview dynamically
function handleImageInput(event) {
    const file = event.target.files[0];
    if (file) {
        form.image = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}

function saveBrand() {
    loading.value = true;
    const routeName = user.id ? 'update.user' : 'store.user';
    form.post(route(routeName, { id: user.id }), {
        onSuccess: () => {
            loading.value = false;
            if (page.props.flash.status === true) {
                successToast(page.props.flash.message);
                form.reset();
            } else {
                errorToast(page.props.flash.message);
            }
        },

        onError: (errors) => {
            loading.value = false;
            if (errors.name) {
                errorToast(errors.name)
            } else if (errors.description) {
                errorToast(errors.description)
            } else {
                errorToast('Failed to save user');
            }
        }
    });
}
</script>


<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between">
                        <div>
                            <h6 class="m-0 font-weight-bold text-info">Save</h6>
                        </div>
                        <div>
                            <Link :href="route('user.page')" class="btn btn-sm btn-info">Back to list
                            </Link>
                        </div>
                    </div>
                    <div class="card-body">
                        <form @submit.prevent="saveBrand()">
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
                                <select class="form-control" v-model="form.role">
                                    <option disabled value="">Select Role</option>
                                    <option value="1">admin</option>
                                    <option value="2">sale</option>
                                    <option value="3">purchase</option>
                                </select>
                                <span v-if="form.errors.role" class="text-danger">{{ form.errors.role }}</span>
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
                                <div class="col-12 col-sm-6 mb-3 position-relative">
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
                                <div class="col-12 col-sm-6 mb-3 position-relative">



                                        <input
                            :type="showPassword ? 'text' : 'password'"
                            class="form-control"
                            v-model="form.password_confirmation"
                            placeholder="Enter Confirm Password"
                            :class="{ 'is-invalid': form.errors.password_confirmation }"
                        />
                        <button type="button"
                            class="btn position-absolute end-0 top-50 translate-middle-y me-2"
                            @click="showPassword = !showPassword"
                            style="background: none; border: none;">
                            <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                        </button>
                        <span v-if="form.errors.password_confirmation" class="text-danger small">{{ form.errors.password_confirmation }}</span>



                                </div>




                            </div>

                            <div class="mb-3">
                                <!-- <label for="image" class="form-label">Profile Image</label>
                                <input type="file" id="image" class="form-control"
                                    @change="(e) => form.image = e.target.files[0]">
                                <span v-if="form.errors.image" class="text-danger">{{ form.errors.image }}</span> -->


                                <label for="image" class="form-label">Profile Image</label>
                                <img :src="imagePreview" alt="Image Preview" class="img-fluid mb-3"
                                    style="display:block; max-height: 80px;" />
                                <input type="file" class="form-control" id="image"
                                    @input="handleImageInput" />

                            </div>


                            <button type="submit align-right" class="btn btn-info"> Save Change </button>
                        </form>
                    </div>
                </div>
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
