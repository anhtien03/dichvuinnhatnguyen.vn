import Swal from 'sweetalert2'
import '../assets/swal-theme.css'
import { useAdminTheme } from '../composables/useAdminTheme'

const { isDarkMode } = useAdminTheme()

function getThemeClass() {
  return isDarkMode.value ? 'swal-pro-dark' : 'swal-pro-light'
}

/**
 * Toast thông báo nhanh ở góc trên bên phải (không chặn thao tác)
 * @param {string} title Nội dung thông báo
 * @param {'success'|'error'|'warning'|'info'} icon Loại icon
 * @param {number} timer Thời gian tự đóng (ms)
 */
export function swalToast(title, icon = 'success', timer = 2800) {
  return Swal.fire({
    toast: true,
    position: 'top-end',
    icon,
    title,
    showConfirmButton: false,
    timer,
    timerProgressBar: true,
    customClass: {
      popup: `swal-pro-toast ${getThemeClass()}`
    },
    didOpen: (toast) => {
      toast.addEventListener('mouseenter', Swal.stopTimer)
      toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
  })
}

/**
 * Modal thông báo Thành công
 */
export function swalSuccess(title, text = '') {
  return Swal.fire({
    icon: 'success',
    title,
    text,
    confirmButtonText: 'Đóng',
    buttonsStyling: false,
    customClass: {
      container: 'swal-backdrop-blur',
      popup: `swal-pro-popup ${getThemeClass()}`,
      title: 'swal-pro-title',
      htmlContainer: 'swal-pro-html',
      actions: 'swal-pro-actions',
      confirmButton: 'swal-btn-confirm swal-btn-primary'
    }
  })
}

/**
 * Modal thông báo Lỗi
 */
export function swalError(title = 'Có lỗi xảy ra', text = '') {
  return Swal.fire({
    icon: 'error',
    title,
    text,
    confirmButtonText: 'Đã hiểu',
    buttonsStyling: false,
    customClass: {
      container: 'swal-backdrop-blur',
      popup: `swal-pro-popup ${getThemeClass()}`,
      title: 'swal-pro-title',
      htmlContainer: 'swal-pro-html',
      actions: 'swal-pro-actions',
      confirmButton: 'swal-btn-confirm swal-btn-blue'
    }
  })
}

/**
 * Modal Cảnh báo
 */
export function swalWarning(title, text = '') {
  return Swal.fire({
    icon: 'warning',
    title,
    text,
    confirmButtonText: 'Kiểm tra lại',
    buttonsStyling: false,
    customClass: {
      container: 'swal-backdrop-blur',
      popup: `swal-pro-popup ${getThemeClass()}`,
      title: 'swal-pro-title',
      htmlContainer: 'swal-pro-html',
      actions: 'swal-pro-actions',
      confirmButton: 'swal-btn-confirm swal-btn-primary'
    }
  })
}

/**
 * Modal Xác nhận hành động (trả về true/false)
 * @param {Object} options
 * @param {string} options.title Tiêu đề xác nhận
 * @param {string} [options.text] Mô tả chi tiết
 * @param {string} [options.html] Mô tả dạng HTML (ưu tiên nếu truyền)
 * @param {'warning'|'question'|'info'|'error'} [options.icon='question']
 * @param {string} [options.confirmText='Xác nhận']
 * @param {string} [options.cancelText='Hủy bỏ']
 * @param {boolean} [options.danger=false] Nếu true, nút xác nhận màu đỏ
 * @returns {Promise<boolean>}
 */
export async function swalConfirm({
  title,
  text = '',
  html = '',
  icon = 'question',
  confirmText = 'Xác nhận',
  cancelText = 'Hủy bỏ',
  danger = false
}) {
  const result = await Swal.fire({
    icon,
    title,
    ...(html ? { html } : { text }),
    showCancelButton: true,
    confirmButtonText: confirmText,
    cancelButtonText: cancelText,
    reverseButtons: true,
    buttonsStyling: false,
    focusCancel: danger,
    customClass: {
      container: 'swal-backdrop-blur',
      popup: `swal-pro-popup ${getThemeClass()}`,
      title: 'swal-pro-title',
      htmlContainer: 'swal-pro-html',
      actions: 'swal-pro-actions',
      confirmButton: `swal-btn-confirm ${danger ? 'swal-btn-danger' : 'swal-btn-primary'}`,
      cancelButton: 'swal-btn-cancel'
    }
  })
  return result.isConfirmed
}

/**
 * Preset Modal Xác nhận Xóa dữ liệu (Chuẩn an toàn cho Admin & ERP)
 * @param {string} itemName Tên đối tượng muốn xóa (VD: "Đơn hàng #DH-001")
 * @param {string} [extraNote='Thao tác này không thể hoàn tác.']
 * @returns {Promise<boolean>}
 */
export function swalDelete(itemName, extraNote = 'Thao tác này không thể hoàn tác sau khi thực hiện.') {
  return swalConfirm({
    title: 'Xác nhận xóa dữ liệu?',
    html: `Bạn có chắc chắn muốn xóa <strong class="text-rose-500">${itemName}</strong>?<br/><span class="text-xs opacity-80">${extraNote}</span>`,
    icon: 'warning',
    confirmText: 'Xóa vĩnh viễn',
    cancelText: 'Giữ lại',
    danger: true
  })
}

export default Swal
