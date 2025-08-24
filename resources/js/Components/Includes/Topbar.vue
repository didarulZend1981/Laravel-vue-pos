<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

const profileData = useForm({
    first_name: '',
    last_name: '',
    email: '',
    mobile: '',
    address: '',
    // image: '',
});

const msg = usePage();

async function fetchProfileData() {
    try {
        const response = await axios.get(route('get.profile'));
        // console.log(response)
        if (response.data.status === 'success') {
            // profileData.value = response.data.user;
            Object.assign(profileData, response.data.user);
        }
    } catch (error) {
        errorToast('Error fetching profile !!');
    }
}

const imagePreview = ref(
    profileData.image ? `/storage/${profileData.image}` : '/asset/img/placeholder-person.jpg'
);

onMounted(() => {
    fetchProfileData();
});

function updateForm() {
    // Client-side validations
    if (!profileData.first_name) {
        errorToast('Please enter your first name!');
    }
    else if (!profileData.last_name) {
        errorToast('Please enter your last name!');
    }
    else if (!profileData.mobile) {
        errorToast('Please enter your mobile number!');
    }
    else if (profileData.mobile.length < 11) {
        errorToast('Mobile number should be 11 digits long!');
    }
    else if (profileData.address.length > 250) {
        errorToast('Address is too long!');
    } else {
        profileData.post(route('update.profile'), {
            onSuccess: () => {
                if (msg.props.flash.status === true) {
                    successToast(msg.props.flash.message);
                    $('#profileModal').modal('hide');
                } else {
                    errorToast(msg.props.flash.message);
                }
            },
            onError: (error) => {
                errorToast('An error occurred during submission!');
                console.error(error)
            },
        });
    }
}

const toggleSidebar = () => {
  const event = new CustomEvent('toggle-sidebar');
  document.dispatchEvent(event);
};
</script>


<template>
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3" @click="toggleSidebar">
            <i class="fa fa-bars"></i>
        </button>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <!-- Dynamically display the first_name here -->
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                        {{ profileData.first_name }} {{ profileData.last_name }}
                    </span>
                    <img class="img-profile rounded-circle" :src="imagePreview">
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profileModal" id="getData">
                        <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                        Profile
                    </a>
                    <div class="dropdown-divider"></div>
                    <Link class="dropdown-item" href="/logout">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                    </Link>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Modal -->
    <div class="modal fade" id="profileModal" tabindex="-1" role="dialog" aria-labelledby="profileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <div class="d-flex justify-content-center">
                        <div
                            style="width: 120px; height: 120px; overflow: hidden; border-radius: 50%; display: flex; justify-content: center; align-items: center; background: #f8f9fa;">
                            <img :src="imagePreview" alt="Image Preview" class="img-fluid"
                                style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    </div>

                    <form id="updateProfile" @submit.prevent="updateForm()" enctype="multipart/form-data">
                        <div class="form-floating mb-3">
                            <label class="form-label" for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" v-model="profileData.first_name">
                        </div>

                        <div class="form-floating mb-3">
                            <label class="form-label" for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" v-model="profileData.last_name">
                        </div>

                        <div class="form-floating mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" v-model="profileData.email" readonly>
                        </div>

                        <div class="form-floating mb-3">
                            <label class="form-label" for="mobile">Phone</label>
                            <input type="number" class="form-control" id="mobile" v-model="profileData.mobile">
                        </div>

                        <div class="form-floating mb-3">
                            <label class="form-label" for="address">Address</label>
                            <textarea class="form-control" id="address" v-model="profileData.address"> </textarea>
                        </div>

                        <!-- Image Upload -->
                        <!-- <div class="mb-3">
                            <label for="productImage" class="form-label">Image</label>
                            <input type="file" class="form-control" id="productImage" accept="image/*"
                                @change="e => (profileData.image = e.target.files[0])">
                        </div> -->

                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-info" data-dismiss="modal"
                                id="closeProfileModal">Cancel</button>
                            <button type="submit" class="btn btn-info">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped></style>
