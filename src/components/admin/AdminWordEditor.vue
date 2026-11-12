<template>
  <div class="space-y-3">
    <!-- TOOLBAR (Word / Docs Style: 2 Rows) -->
    <div
      class="rounded-2xl border overflow-hidden shadow-xs select-none"
      :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47]' : 'bg-slate-100/90 border-slate-200'"
    >
      <!-- ROW 1: Chế độ soạn thảo, Kiểu đoạn, Phông chữ, Cỡ chữ, B/I/U/S, Màu chữ & Highlight, Xóa định dạng -->
      <div
        class="flex flex-wrap items-center gap-1.5 p-2 border-b"
        :class="isDarkMode ? 'border-[#2E3A47] bg-[#24303F]' : 'border-slate-200/80 bg-white'"
      >
        <!-- Chuyển đổi Trực quan (Word) vs Mã HTML -->
        <div class="flex items-center gap-0.5 pr-2 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <button
            type="button"
            @click="switchEditorMode('visual')"
            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            :class="editorMode === 'visual' ? 'bg-brand-gold text-slate-950 shadow-xs' : 'text-slate-400 hover:text-white'"
            title="Soạn thảo trực quan (Word / Google Docs)"
          >
            <Sparkles class="w-3.5 h-3.5" />
            <span>Word</span>
          </button>
          <button
            type="button"
            @click="switchEditorMode('html')"
            class="px-2.5 py-1 rounded-lg text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
            :class="editorMode === 'html' ? 'bg-brand-gold text-slate-950 shadow-xs' : 'text-slate-400 hover:text-white'"
            title="Chỉnh sửa mã nguồn HTML"
          >
            <span>&lt;/&gt; HTML</span>
          </button>
        </div>

        <!-- Kiểu đoạn văn (Heading / Paragraph) -->
        <select
          v-model="selectedHeading"
          @change="applyHeadingBlock"
          class="rounded-lg px-2 py-1 text-xs font-bold border focus:outline-none focus:border-brand-gold cursor-pointer"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          title="Kiểu đoạn văn / Tiêu đề"
        >
          <option value="p">Đoạn văn thường</option>
          <option value="h1">Tiêu đề lớn (H1)</option>
          <option value="h2">Tiêu đề mục (H2)</option>
          <option value="h3">Tiêu đề phụ (H3)</option>
          <option value="h4">Tiêu đề nhỏ (H4)</option>
          <option value="blockquote">Trích dẫn (“ ”)</option>
        </select>

        <!-- Phông chữ (Font Family) -->
        <select
          v-model="selectedFontFamily"
          @change="applyFontFamily"
          class="rounded-lg px-2 py-1 text-xs font-semibold border focus:outline-none focus:border-brand-gold cursor-pointer max-w-[125px]"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
          title="Chọn phông chữ (Font)"
        >
          <option value="">Phông mặc định</option>
          <option value="Arial, sans-serif">Arial</option>
          <option value="'Times New Roman', serif">Times New Roman</option>
          <option value="Tahoma, sans-serif">Tahoma</option>
          <option value="Verdana, sans-serif">Verdana</option>
          <option value="Georgia, serif">Georgia</option>
          <option value="'Courier New', monospace">Courier New</option>
        </select>

        <!-- Cỡ chữ (Font Size px + A+ / A-) -->
        <div class="flex items-center gap-1 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <select
            v-model="selectedFontSize"
            @change="applyFontSizePx(selectedFontSize)"
            class="rounded-lg px-2 py-1 text-xs font-bold border focus:outline-none focus:border-brand-gold cursor-pointer"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-brand-gold' : 'bg-slate-50 border-slate-200 text-slate-900'"
            title="Cỡ chữ (px)"
          >
            <option value="12px">12px</option>
            <option value="13px">13px</option>
            <option value="14px">14px (Chuẩn)</option>
            <option value="15px">15px</option>
            <option value="16px">16px</option>
            <option value="18px">18px (Lớn)</option>
            <option value="20px">20px</option>
            <option value="24px">24px (Tiêu đề)</option>
            <option value="28px">28px</option>
            <option value="32px">32px</option>
            <option value="36px">36px</option>
          </select>

          <button
            type="button"
            @mousedown.prevent="stepFontSize(1)"
            class="px-2 py-1 rounded-lg text-xs font-black border transition-colors cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            title="Tăng cỡ chữ (A+)"
          >
            A<sup class="text-[9px] text-brand-gold">+</sup>
          </button>
          <button
            type="button"
            @mousedown.prevent="stepFontSize(-1)"
            class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
            title="Giảm cỡ chữ (A-)"
          >
            A<sup class="text-[9px]">-</sup>
          </button>
        </div>

        <!-- B / I / U / S -->
        <div class="flex items-center gap-1 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <button
            type="button"
            @mousedown.prevent="applyFormat('bold')"
            class="w-7 h-7 rounded-lg text-xs font-black border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            title="In đậm (Ctrl+B)"
          >
            B
          </button>
          <button
            type="button"
            @mousedown.prevent="applyFormat('italic')"
            class="w-7 h-7 rounded-lg text-xs italic font-serif font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            title="In nghiêng (Ctrl+I)"
          >
            I
          </button>
          <button
            type="button"
            @mousedown.prevent="applyFormat('underline')"
            class="w-7 h-7 rounded-lg text-xs underline font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-slate-50 border-slate-200 text-slate-800'"
            title="Gạch chân (Ctrl+U)"
          >
            U
          </button>
          <button
            type="button"
            @mousedown.prevent="execCmd('strikeThrough')"
            class="w-7 h-7 rounded-lg text-xs line-through font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center justify-center"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-300' : 'bg-slate-50 border-slate-200 text-slate-700'"
            title="Gạch ngang chữ"
          >
            S
          </button>
        </div>

        <!-- Màu chữ (Text Color) & Tô nền (Highlight) -->
        <div class="flex items-center gap-1.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
          <!-- Quick Text Colors -->
          <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-lg border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <span class="text-[10px] font-black pr-0.5" :style="{ color: currentTextColor }">A</span>
            <button
              v-for="c in QUICK_TEXT_COLORS"
              :key="c.hex"
              type="button"
              @mousedown.prevent="applyTextColor(c.hex)"
              class="w-3.5 h-3.5 rounded-full border border-white/20 cursor-pointer hover:scale-125 transition-transform"
              :style="{ backgroundColor: c.hex }"
              :title="'Màu chữ: ' + c.label"
            ></button>
            <input
              type="color"
              v-model="currentTextColor"
              @input="applyTextColor(currentTextColor)"
              class="w-4 h-4 p-0 border-0 bg-transparent cursor-pointer"
              title="Chọn màu chữ tùy ý"
            />
          </div>

          <!-- Quick Highlight Colors -->
          <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-lg border" :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'">
            <span class="text-[10px] font-bold px-1 rounded bg-yellow-300 text-slate-900" title="Tô nền chữ (Highlight)">ab</span>
            <button
              v-for="hc in QUICK_HIGHLIGHT_COLORS"
              :key="hc.hex"
              type="button"
              @mousedown.prevent="applyHighlightColor(hc.hex)"
              class="w-3.5 h-3.5 rounded border border-slate-300 cursor-pointer hover:scale-125 transition-transform"
              :style="{ backgroundColor: hc.hex }"
              :title="'Tô nền: ' + hc.label"
            ></button>
            <button
              type="button"
              @mousedown.prevent="applyHighlightColor('transparent')"
              class="text-[10px] text-slate-400 hover:text-red-400 px-0.5 cursor-pointer"
              title="Xóa màu nền tô sáng"
            >
              ✕
            </button>
          </div>
        </div>

        <!-- Xóa định dạng (Clear Formatting) -->
        <button
          type="button"
          @mousedown.prevent="clearSelectedFormatting"
          class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-red-400 text-slate-400 hover:text-red-400"
          :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
          title="Xóa mọi định dạng vùng đang chọn (Đưa về chữ chuẩn)"
        >
          <Eraser class="w-3.5 h-3.5" />
        </button>
      </div>

      <!-- ROW 2: Căn lề (Trái/Giữa/Phải/Đều), Danh sách (Chấm/Số), Thụt lề, Bảng, Link, Lưu ý & Chèn Ảnh -->
      <div class="flex flex-wrap items-center justify-between gap-2 p-2">
        <div class="flex flex-wrap items-center gap-1">
          <!-- Alignment: Left, Center, Right, Justify -->
          <div class="flex items-center gap-0.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <button
              type="button"
              @mousedown.prevent="execCmd('justifyLeft')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Căn lề trái"
            >
              <AlignLeft class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('justifyCenter')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Căn giữa"
            >
              <AlignCenter class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('justifyRight')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Căn lề phải"
            >
              <AlignRight class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('justifyFull')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Căn đều 2 bên (Justify)"
            >
              <AlignJustify class="w-3.5 h-3.5" />
            </button>
          </div>

          <!-- Lists & Indent -->
          <div class="flex items-center gap-0.5 pr-1.5 border-r" :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'">
            <button
              type="button"
              @mousedown.prevent="applyFormat('ul')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Danh sách dấu chấm tròn (Bullets)"
            >
              <List class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="applyFormat('ol')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Danh sách đánh số (1, 2, 3...)"
            >
              <ListOrdered class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('outdent')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Giảm thụt lề"
            >
              <Outdent class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('indent')"
              class="p-1.5 rounded-lg border transition-colors cursor-pointer hover:border-brand-gold"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Tăng thụt lề đầu dòng"
            >
              <Indent class="w-3.5 h-3.5" />
            </button>
          </div>

          <!-- Bảng biểu, Liên kết, Đường kẻ ngang, Lưu ý -->
          <div class="flex flex-wrap items-center gap-1">
            <button
              type="button"
              @click="insertSampleTable"
              class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Chèn bảng thông số kỹ thuật / bảng giá"
            >
              <Table class="w-3.5 h-3.5 text-brand-gold" />
              <span>Bảng</span>
            </button>
            <button
              type="button"
              @click="insertHyperlink"
              class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Chèn liên kết (Link)"
            >
              <Link2 class="w-3.5 h-3.5 text-blue-400" />
              <span>Link</span>
            </button>
            <button
              type="button"
              @mousedown.prevent="execCmd('insertHorizontalRule')"
              class="px-2 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer hover:border-brand-gold flex items-center gap-1"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-slate-50 border-slate-200 text-slate-700'"
              title="Chèn đường kẻ ngang phân cách"
            >
              <Minus class="w-3.5 h-3.5" />
            </button>
            <button
              type="button"
              @click="applyFormat('tip')"
              class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors cursor-pointer text-amber-500 hover:border-amber-500"
              :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47]' : 'bg-slate-50 border-slate-200'"
              title="Chèn hộp lưu ý kỹ thuật"
            >
              💡 Lưu ý
            </button>
          </div>
        </div>

        <!-- Image Insert Buttons -->
        <div class="flex flex-wrap items-center gap-1.5">
          <button
            type="button"
            @click="emit('openMedia')"
            class="px-2.5 py-1.5 rounded-lg bg-brand-gold/15 hover:bg-brand-gold/25 text-amber-500 border border-brand-gold/30 font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors"
          >
            <Images class="w-3.5 h-3.5" />
            <span>Chèn Ảnh Thư Viện</span>
          </button>

          <button
            type="button"
            @click="triggerUpload"
            :disabled="isUploading"
            class="px-2.5 py-1.5 rounded-lg bg-brand-gold hover:bg-amber-400 text-slate-950 font-bold text-xs flex items-center gap-1.5 cursor-pointer transition-colors disabled:opacity-50"
          >
            <Loader2 v-if="isUploading" class="w-3.5 h-3.5 animate-spin" />
            <UploadCloud v-else class="w-3.5 h-3.5" />
            <span>{{ isUploading ? 'Đang tải...' : 'Thêm Ảnh Từ Máy' }}</span>
          </button>
          <input
            ref="fileInputRef"
            type="file"
            accept="image/*"
            multiple
            class="hidden"
            @change="handleFileUpload"
          />
        </div>
      </div>

      <!-- CONTEXTUAL IMAGE TOOLBAR (Hiện ra khi click vào 1 ảnh trong bài viết/mô tả) -->
      <div
        v-if="selectedImageEl"
        class="flex flex-wrap items-center justify-between gap-2 px-3 py-2 bg-amber-500/10 text-xs border-t"
        :class="isDarkMode ? 'border-[#2E3A47]' : 'border-slate-200'"
      >
        <div class="flex flex-wrap items-center gap-1.5">
          <span class="font-bold text-amber-500">🖼 Tùy chỉnh ảnh đang chọn:</span>
          <button
            v-for="w in ['25%', '50%', '75%', '100%']"
            :key="w"
            type="button"
            @click="resizeSelectedImage(w)"
            class="px-2 py-0.5 rounded border font-bold cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
          >
            Rộng {{ w }}
          </button>
          <span class="text-slate-500">|</span>
          <button
            type="button"
            @click="alignSelectedImage('left')"
            class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
          >
            Căn trái
          </button>
          <button
            type="button"
            @click="alignSelectedImage('center')"
            class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
          >
            Căn giữa
          </button>
          <button
            type="button"
            @click="alignSelectedImage('right')"
            class="px-2 py-0.5 rounded border font-semibold cursor-pointer hover:border-brand-gold"
            :class="isDarkMode ? 'bg-[#1C2434] border-[#2E3A47] text-slate-200' : 'bg-white border-slate-200 text-slate-700'"
          >
            Căn phải
          </button>
        </div>
        <button
          type="button"
          @click="removeSelectedImage"
          class="px-2.5 py-0.5 rounded bg-red-500/20 text-red-400 hover:bg-red-500/30 font-bold cursor-pointer"
        >
          Xóa ảnh này
        </button>
      </div>
    </div>

    <!-- VISUAL WYSIWYG EDITOR -->
    <div
      v-show="editorMode === 'visual'"
      ref="visualEditorRef"
      contenteditable="true"
      @input="onVisualInput"
      @blur="onVisualInput"
      @mouseup="onSelectionChange"
      @keyup="onSelectionChange"
      @click="onEditorClick"
      class="w-full overflow-y-auto rounded-xl p-4 sm:p-5 text-sm border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all leading-relaxed visual-word-editor"
      :style="{ minHeight: `${minHeight}px`, maxHeight: `${maxHeight}px` }"
      :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-slate-100' : 'bg-white border-slate-200 text-slate-800'"
      :data-placeholder="placeholder"
    ></div>

    <!-- RAW HTML CODE EDITOR -->
    <textarea
      v-show="editorMode === 'html'"
      ref="textareaRef"
      :value="modelValue"
      @input="onRawInput($event.target.value)"
      :rows="rows"
      :placeholder="placeholder"
      class="w-full rounded-xl px-4 py-3 text-xs font-mono border focus:outline-none focus:ring-2 focus:ring-brand-gold transition-all leading-relaxed"
      :class="isDarkMode ? 'bg-[#1A222C] border-[#2E3A47] text-white' : 'bg-white border-slate-200 text-slate-800'"
    ></textarea>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, nextTick } from 'vue'
import {
  Sparkles,
  AlignLeft,
  AlignCenter,
  AlignRight,
  AlignJustify,
  List,
  ListOrdered,
  Outdent,
  Indent,
  Table,
  Link2,
  Minus,
  Images,
  UploadCloud,
  Loader2,
  Eraser
} from 'lucide-vue-next'
import { api } from '../../services/api'
import { useAdminTheme } from '../../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Nhập nội dung chi tiết...' },
  folder: { type: String, default: 'content' },
  entityName: { type: String, default: '' },
  minHeight: { type: Number, default: 280 },
  maxHeight: { type: Number, default: 600 },
  rows: { type: Number, default: 12 }
})

const emit = defineEmits(['update:modelValue', 'openMedia'])

const editorMode = ref('visual')
const visualEditorRef = ref(null)
const textareaRef = ref(null)
const fileInputRef = ref(null)
const isUploading = ref(false)

const selectedHeading = ref('p')
const selectedFontFamily = ref('')
const selectedFontSize = ref('14px')
const currentTextColor = ref('#000000')
const selectedImageEl = ref(null)
let savedEditorRange = null

const QUICK_TEXT_COLORS = [
  { label: 'Đen mặc định', hex: '#0f172a' },
  { label: 'Đỏ nổi bật', hex: '#dc2626' },
  { label: 'Vàng kim thương hiệu', hex: '#d97706' },
  { label: 'Xanh dương công nghệ', hex: '#2563eb' },
  { label: 'Xanh lá cam kết', hex: '#16a34a' }
]

const QUICK_HIGHLIGHT_COLORS = [
  { label: 'Vàng rực', hex: '#fef08a' },
  { label: 'Xanh lơ nhạt', hex: '#bfdbfe' },
  { label: 'Xanh ngọc', hex: '#bbf7d0' },
  { label: 'Hồng phấn', hex: '#fbcfe8' }
]

function syncVisualFromModel() {
  if (visualEditorRef.value && visualEditorRef.value.innerHTML !== props.modelValue) {
    visualEditorRef.value.innerHTML = props.modelValue || ''
  }
}

function onVisualInput() {
  if (visualEditorRef.value) {
    emit('update:modelValue', visualEditorRef.value.innerHTML)
  }
}

function onRawInput(val) {
  emit('update:modelValue', val)
  if (visualEditorRef.value) {
    visualEditorRef.value.innerHTML = val || ''
  }
}

function switchEditorMode(mode) {
  if (mode === 'html' && editorMode.value === 'visual') {
    onVisualInput()
  } else if (mode === 'visual' && editorMode.value === 'html') {
    syncVisualFromModel()
  }
  editorMode.value = mode
}

function saveEditorSelection() {
  const sel = window.getSelection()
  if (sel && sel.rangeCount > 0 && visualEditorRef.value && visualEditorRef.value.contains(sel.anchorNode)) {
    savedEditorRange = sel.getRangeAt(0).cloneRange()
  }
}

function restoreEditorSelection() {
  if (savedEditorRange && visualEditorRef.value) {
    const sel = window.getSelection()
    sel.removeAllRanges()
    sel.addRange(savedEditorRange)
  }
}

function onSelectionChange() {
  saveEditorSelection()
}

function onEditorClick(e) {
  const target = e.target
  if (target && target.tagName === 'IMG') {
    selectedImageEl.value = target
  } else {
    selectedImageEl.value = null
  }
  saveEditorSelection()
}

function resizeSelectedImage(widthStr) {
  if (!selectedImageEl.value) return
  selectedImageEl.value.style.width = widthStr
  selectedImageEl.value.style.maxWidth = '100%'
  selectedImageEl.value.style.height = 'auto'
  onVisualInput()
}

function alignSelectedImage(align) {
  if (!selectedImageEl.value) return
  const img = selectedImageEl.value
  img.classList.remove('mx-auto', 'ml-auto', 'mr-auto', 'float-left', 'float-right', 'block')
  img.style.display = 'block'
  if (align === 'center') {
    img.style.marginLeft = 'auto'
    img.style.marginRight = 'auto'
  } else if (align === 'right') {
    img.style.marginLeft = 'auto'
    img.style.marginRight = '0'
  } else {
    img.style.marginLeft = '0'
    img.style.marginRight = 'auto'
  }
  onVisualInput()
}

function removeSelectedImage() {
  if (!selectedImageEl.value) return
  const parent = selectedImageEl.value.closest('figure') || selectedImageEl.value
  parent.remove()
  selectedImageEl.value = null
  onVisualInput()
}

function execCmd(command, value = null) {
  if (editorMode.value === 'visual' && visualEditorRef.value) {
    visualEditorRef.value.focus()
    restoreEditorSelection()
    document.execCommand(command, false, value)
    onSelectionChange()
    onVisualInput()
  }
}

function applyHeadingBlock() {
  const tag = selectedHeading.value || 'p'
  if (editorMode.value === 'visual') {
    execCmd('formatBlock', `<${tag}>`)
  }
}

function applyFontFamily() {
  if (!selectedFontFamily.value) return
  execCmd('fontName', selectedFontFamily.value)
}

function applyFontSizePx(sizePx) {
  if (editorMode.value !== 'visual' || !visualEditorRef.value) return
  visualEditorRef.value.focus()
  restoreEditorSelection()
  const sel = window.getSelection()
  if (!sel || !sel.rangeCount || sel.isCollapsed) return

  const span = document.createElement('span')
  span.style.fontSize = sizePx
  const range = sel.getRangeAt(0)
  try {
    range.surroundContents(span)
  } catch (e) {
    document.execCommand('fontSize', false, '3')
    const fontEls = visualEditorRef.value.querySelectorAll('font[size="3"]')
    fontEls.forEach(el => {
      el.removeAttribute('size')
      el.style.fontSize = sizePx
    })
  }
  onSelectionChange()
  onVisualInput()
}

function stepFontSize(delta) {
  const cur = parseInt(selectedFontSize.value, 10) || 14
  const next = Math.max(11, Math.min(48, cur + delta))
  selectedFontSize.value = `${next}px`
  applyFontSizePx(selectedFontSize.value)
}

function applyTextColor(colorHex) {
  currentTextColor.value = colorHex
  execCmd('foreColor', colorHex)
}

function applyHighlightColor(colorHex) {
  execCmd('hiliteColor', colorHex)
}

function clearSelectedFormatting() {
  execCmd('removeFormat')
}

function applyFormat(type) {
  if (editorMode.value === 'visual' && visualEditorRef.value) {
    restoreEditorSelection()
    const sel = window.getSelection()
    const hasSelection = sel && sel.toString().trim().length > 0 && visualEditorRef.value.contains(sel.anchorNode)

    if (type === 'bold') return execCmd('bold')
    if (type === 'italic') return execCmd('italic')
    if (type === 'underline') return execCmd('underline')
    if (type === 'ul') return execCmd('insertUnorderedList')
    if (type === 'ol') return execCmd('insertOrderedList')
    if ((type === 'h2' || type === 'h3') && hasSelection) {
      return execCmd('formatBlock', `<${type}>`)
    }
  }

  let snippet = ''
  switch (type) {
    case 'h2':
      snippet = '<h2>Tiêu đề mục chính</h2><p>Nội dung chi tiết...</p>'
      break
    case 'h3':
      snippet = '<h3>Tiêu đề mục phụ</h3><p>Nội dung chi tiết...</p>'
      break
    case 'ul':
      snippet = '<ul><li>Ưu điểm / quy cách thứ nhất</li><li>Ưu điểm / quy cách thứ hai</li></ul>'
      break
    case 'ol':
      snippet = '<ol><li>Bước 1 / Quy cách 1</li><li>Bước 2 / Quy cách 2</li></ol>'
      break
    case 'tip':
      snippet = '<div class="p-4 rounded-xl border border-amber-300 bg-amber-50 text-slate-800 my-4 text-xs font-medium">💡 <strong>Lưu ý từ Xưởng In Nhật Nguyên:</strong> Hỗ trợ thiết kế & kiểm tra file chuẩn màu CMYK miễn phí trước khi in.</div><p></p>'
      break
  }

  if (snippet) {
    insertHtml(snippet)
  }
}

function insertSampleTable() {
  const tableHtml = `
<table class="w-full my-4 border-collapse border border-slate-300 text-xs text-left">
  <thead>
    <tr class="bg-slate-100 font-bold">
      <th class="border border-slate-300 p-2.5">Quy Cách In Ấn</th>
      <th class="border border-slate-300 p-2.5">Thông Số Kỹ Thuật</th>
      <th class="border border-slate-300 p-2.5">Ghi Chú Xưởng In</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td class="border border-slate-300 p-2 font-semibold">Chất liệu giấy</td>
      <td class="border border-slate-300 p-2">Couche 300gsm / Kraft / Ivory</td>
      <td class="border border-slate-300 p-2">Cứng cáp, chuẩn màu</td>
    </tr>
    <tr>
      <td class="border border-slate-300 p-2 font-semibold">Công nghệ in</td>
      <td class="border border-slate-300 p-2">In Offset 4 màu CMYK 2 mặt</td>
      <td class="border border-slate-300 p-2">Sắc nét, màu đồng đều</td>
    </tr>
    <tr>
      <td class="border border-slate-300 p-2 font-semibold">Gia công sau in</td>
      <td class="border border-slate-300 p-2">Cán màng mờ / bóng 2 mặt</td>
      <td class="border border-slate-300 p-2">Chống nước, chống trầy</td>
    </tr>
  </tbody>
</table>
<p></p>`
  insertHtml(tableHtml)
}

function insertHyperlink() {
  const url = prompt('Nhập địa chỉ liên kết (URL):', 'https://dichvuinnhatnguyen.vn')
  if (url) {
    execCmd('createLink', url)
  }
}

function insertHtml(htmlSnippet) {
  if (editorMode.value === 'visual' && visualEditorRef.value) {
    restoreEditorSelection()
    const sel = window.getSelection()
    if (sel && sel.rangeCount > 0 && visualEditorRef.value.contains(sel.anchorNode)) {
      document.execCommand('insertHTML', false, htmlSnippet)
    } else {
      visualEditorRef.value.innerHTML += htmlSnippet
    }
    onSelectionChange()
    onVisualInput()
  } else {
    const nextVal = (props.modelValue || '') + '\n' + htmlSnippet
    emit('update:modelValue', nextVal)
  }
}

function triggerUpload() {
  fileInputRef.value?.click()
}

async function handleFileUpload(e) {
  const files = Array.from(e.target.files || [])
  if (!files.length) return

  isUploading.value = true
  try {
    for (const file of files) {
      const formData = new FormData()
      formData.append('file', file)
      formData.append('folder', props.folder)
      const res = await api.uploadAdminMedia(formData)
      if (res.data?.url) {
        const titleName = props.entityName || 'Hình ảnh minh họa thực tế'
        const imgHtml = `<figure class="my-5 text-center"><img src="${res.data.url}" alt="${titleName}" class="rounded-2xl max-w-full h-auto mx-auto shadow-md" /><figcaption class="text-xs text-slate-500 mt-2 italic">${titleName} tại Xưởng In Nhật Nguyên</figcaption></figure><p></p>`
        insertHtml(imgHtml)
      }
    }
  } catch (err) {
    alert('Lỗi tải ảnh: ' + (err.data?.message || err.message))
  } finally {
    isUploading.value = false
    if (fileInputRef.value) fileInputRef.value.value = ''
  }
}

// Expose insertHtml for parent component (e.g. Media Library Modal selection)
defineExpose({
  insertHtml
})

watch(
  () => props.modelValue,
  (newVal) => {
    if (editorMode.value === 'visual' && visualEditorRef.value) {
      if (visualEditorRef.value.innerHTML !== newVal) {
        visualEditorRef.value.innerHTML = newVal || ''
      }
    }
  }
)

onMounted(() => {
  nextTick(() => {
    syncVisualFromModel()
  })
})
</script>

<style scoped>
.visual-word-editor:empty:before {
  content: attr(data-placeholder);
  color: #94a3b8;
  pointer-events: none;
}

.visual-word-editor :deep(h1) {
  font-size: 1.45rem;
  font-weight: 900;
  margin-top: 1.25rem;
  margin-bottom: 0.65rem;
  line-height: 1.35;
}

.visual-word-editor :deep(h2) {
  font-size: 1.25rem;
  font-weight: 800;
  margin-top: 1.15rem;
  margin-bottom: 0.55rem;
  line-height: 1.35;
}

.visual-word-editor :deep(h3) {
  font-size: 1.08rem;
  font-weight: 700;
  margin-top: 0.95rem;
  margin-bottom: 0.45rem;
  line-height: 1.35;
}

.visual-word-editor :deep(h4) {
  font-size: 0.98rem;
  font-weight: 700;
  margin-top: 0.75rem;
  margin-bottom: 0.35rem;
}

.visual-word-editor :deep(p) {
  margin-bottom: 0.75rem;
  line-height: 1.7;
}

.visual-word-editor :deep(ul) {
  list-style-type: disc;
  padding-left: 1.5rem;
  margin-bottom: 0.85rem;
}

.visual-word-editor :deep(ol) {
  list-style-type: decimal;
  padding-left: 1.5rem;
  margin-bottom: 0.85rem;
}

.visual-word-editor :deep(li) {
  margin-bottom: 0.35rem;
}

.visual-word-editor :deep(blockquote) {
  border-left: 4px solid #f59e0b;
  padding: 0.65rem 1rem;
  margin: 1rem 0;
  font-style: italic;
  background: rgba(245, 158, 11, 0.08);
  border-radius: 0 0.75rem 0.75rem 0;
}

.visual-word-editor :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
}

.visual-word-editor :deep(th),
.visual-word-editor :deep(td) {
  border: 1px solid #cbd5e1;
  padding: 0.6rem 0.8rem;
}

.visual-word-editor :deep(img) {
  max-width: 100%;
  height: auto;
  border-radius: 0.85rem;
  margin: 0.75rem auto;
  display: block;
  cursor: pointer;
}

.visual-word-editor :deep(img:hover) {
  outline: 2px solid #f59e0b;
}
</style>

