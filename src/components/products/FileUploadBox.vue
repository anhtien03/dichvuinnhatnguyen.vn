<template>
  <div class="space-y-3">
    <!-- Drag & Drop Zone -->
    <div
      @dragover.prevent="isDragging = true"
      @dragleave.prevent="isDragging = false"
      @drop.prevent="onDrop"
      :class="[
        'border-2 border-dashed rounded-2xl p-6 text-center transition-all cursor-pointer',
        isDragging
          ? 'border-brand-gold bg-brand-gold-light/40 scale-[1.01]'
          : 'border-slate-300 hover:border-brand-blue bg-slate-50/50 hover:bg-slate-50'
      ]"
      @click="fileInputRef?.click()"
    >
      <input
        ref="fileInputRef"
        type="file"
        multiple
        class="hidden"
        accept=".pdf,.ai,.psd,.cdr,.zip,.rar,.7z,.jpg,.jpeg,.png,.tiff"
        @change="onFileChange"
      />

      <div class="flex flex-col items-center justify-center">
        <div class="w-12 h-12 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-brand-blue mb-3">
          <UploadCloud class="w-6 h-6" />
        </div>
        <p class="text-sm font-bold text-slate-800">
          Kéo thả file thiết kế vào đây hoặc <span class="text-brand-blue underline">chọn từ máy tính</span>
        </p>
        <p class="text-xs text-slate-400 mt-1">
          Hỗ trợ: PDF, AI, PSD, Corel (CDR), ZIP, RAR, JPG, PNG (Tối đa 50MB/file)
        </p>
      </div>
    </div>

    <!-- Uploaded files list -->
    <div v-if="files.length > 0" class="space-y-2">
      <div
        v-for="(file, idx) in files"
        :key="idx"
        class="flex items-center justify-between p-3 rounded-xl bg-white border border-slate-200 shadow-sm text-xs"
      >
        <div class="flex items-center gap-2.5 truncate mr-3">
          <FileText class="w-4 h-4 text-brand-blue shrink-0" />
          <div class="truncate">
            <span class="font-semibold text-slate-800 truncate block">{{ file.name }}</span>
            <span class="text-[10px] text-slate-400">{{ formatFileSize(file.size) }}</span>
          </div>
        </div>
        <button
          type="button"
          @click="removeFile(idx)"
          class="p-1 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors shrink-0"
          title="Xóa file"
        >
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { UploadCloud, FileText, Trash2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['update:modelValue'])

const isDragging = ref(false)
const fileInputRef = ref(null)
const files = ref([...props.modelValue])

function onFileChange(e) {
  if (e.target.files) {
    addFiles(Array.from(e.target.files))
  }
}

function onDrop(e) {
  isDragging.value = false
  if (e.dataTransfer.files) {
    addFiles(Array.from(e.dataTransfer.files))
  }
}

function addFiles(newFiles) {
  // Check max 50MB
  const validFiles = newFiles.filter(f => {
    if (f.size > 50 * 1024 * 1024) {
      alert(`File "${f.name}" vượt quá dung lượng cho phép (tối đa 50MB). Vui lòng gửi link Google Drive/Dropbox qua ghi chú.`)
      return false
    }
    return true
  })
  files.value = [...files.value, ...validFiles]
  emit('update:modelValue', files.value)
}

function removeFile(index) {
  files.value.splice(index, 1)
  emit('update:modelValue', files.value)
}

function formatFileSize(bytes) {
  if (bytes === 0) return '0 B'
  const k = 1024
  const sizes = ['B', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i]
}
</script>

