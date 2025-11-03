<template>
  <div class="col-xl-3 col-md-6 mb-4">
                <div class="card shadow h-100 py-2">
                          <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ title }}
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ formattedQty }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    {{ formattedValue }}
                                <span class="font-weight-bold text-gray-800" style="font-size: 1rem;">৳</span>
                                </div>
                                </div>
                            </div>

                    </div>
    </div>

</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  quantity: {
    type: Number,
    required: true
  },
  value: {
    type: Number,
    required: true
  },

  locale: {
    type: String,
    default: 'en-US'
  }
})

const formattedQty = computed(() => props.quantity.toLocaleString(props.locale))
const formattedValue = computed(() => props.value.toLocaleString(props.locale))
</script>
