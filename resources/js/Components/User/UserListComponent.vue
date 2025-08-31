<script setup>
import { ref, computed } from "vue";
import { usePage, router, Link ,useForm} from "@inertiajs/vue3";
import Loader from "../Loader/Loader.vue";





const isRoleModalOpen = ref(false); // Modal visibility state
const selectedRole = ref(null); // Selected role in modal
const userId = ref(null); // Store selected user ID

// Open Role Modal and pass user ID
const openRoleModal = (id,currentRole) => {
  userId.value = id; // Store the user ID to update the role
  selectedRole.value = 0;
  isRoleModalOpen.value = true;

};

// Close the Role Modal
const closeRoleModal = () => {
  isRoleModalOpen.value = false;
};

const updateRole = () => {
  const form = useForm({
    role: selectedRole.value,
  });

  form.put(route('update.role', { id: userId.value }), {
    onSuccess: () => {
      console.log('Role updated successfully');
      closeRoleModal(); // Close modal after success
    },
    onError: (errors) => {
      console.log('Failed to update role:', errors);
    },
  });
};


const loading = ref(false);
const page = usePage();
const Header = [
    { text: "No", value: "no" },
    { text: "image", value: "image" },
    { text: "User Name", value: "name" },
    { text: "Role", value: "role" },
    { text: "Email", value: "email" },
    { text: "mobile", value: "mobile" },
    { text: "address", value: "address" },
    { text: "Action", value: "number" },
];

const roleMap = {
    1: 'admin',
    2: 'sale',
    3: 'purchase',
};

const Item = computed(() => {
    return page.props.users?.map((users, index) => ({
        no: index + 1,
        name: users.first_name+' '+users.first_name,
        role: roleMap[users.role] || 'unknown',
        email: users.email,
        mobile: users.mobile,
        address: users.address,
        image: users.image,

        id: users.id,
    })) || [];
});

//serch functionality
const searchValue = ref("");
const searchField = ref(["name"]);

// delete brand/company
const deleteBrand = (id) => {
    loading.value = true;
    if (confirm('Are you sure to delete this brand?')) {
        router.delete(route('delete.brand', { id: id }), {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                if (page.props.flash.status === true) {
                    successToast(page.props.flash.message);
                } else {
                    errorToast(page.props.flash.message || 'Failed to delete brand!');
                }
            },
            onError: (errors) => {
                loading.value = false;
                errorToast(errors.message || 'Failed to delete brand!');
            }
        });
    }
}
</script>

<template>

  <!-- Modal -->
  <div v-if="isRoleModalOpen" class="modal fade show" style="display: block;" tabindex="-1" id="RoleModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update User Role</h5>
          <button type="button" class="btn-close" @click="closeRoleModal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="updateRole">
            <div class="mb-3">
              <label for="role" class="form-label">Select Role</label>
              <select id="role" class="form-control" v-model="selectedRole">
                <option disabled value="0">Select Role</option>
                <option value="1">Admin</option>
                <option value="2">Sale</option>
                <option value="3">Purchase</option>
              </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Role</button>
          </form>
        </div>
      </div>
    </div>
  </div>

    <div class="container-fluid">
        <h1 class="h3 mb-3 text-gray-800">Users</h1>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 align-items-center d-flex justify-content-between">
                <input placeholder="Search..." class="form-control w-auto form-control-sm" type="text"
                    v-model="searchValue">

                <Link class="btn btn-sm btn-info" :href="route('show.save.user')">
                Add User </Link>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <EasyDataTable buttons-pagination alternating :headers="Header" :items="Item" border-cell
                        theme-color="#36b9cc" :rows-per-page="15" :search-field="searchField"
                        :search-value="searchValue">


                        <template #item-image="{ image }">
                            <img :src="image ? `/storage/user/${image}` : '/asset/img/placeholder.jpg'"
                                alt="User Image" style="width: 50px; height: 50px; object-fit: cover;" class="p-1">
                        </template>

                        <template #item-number="{ id,role}">
                            <Link type="button" class="btn btn-sm btn-outline-success"
                                :href="route('show.save.user', { id: id })">
                            <i class="fa fa-edit"></i>
                            </Link>
                            <!-- <a href="#" class="dropdown-item" @click="openRoleModal(id)">
                                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Role
                            </a> -->

                            <button type="button" class="btn btn-sm btn-outline-danger ml-2" @click="openRoleModal(id,role)">
    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i> Role
</button>
                            <button class="btn btn-sm btn-outline-danger ml-2">
                                <i class="fa fa-trash"></i>
                            </button>
                        </template>
                    </EasyDataTable>
                </div>
            </div>
        </div>
    </div>

    <Loader v-if="loading" />


</template>
