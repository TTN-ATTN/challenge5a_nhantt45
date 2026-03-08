// --- TOAST UI ---
function showToast(message, type) {
    var toast = document.getElementById("toast-container");
    if (!toast) return;
    toast.innerText = message;
    toast.classList.remove("toast-error", "toast-success");
    if (type === 'error') toast.classList.add("toast-error");
    if (type === 'success') toast.classList.add("toast-success");
    toast.classList.add("show");
    setTimeout(function() { toast.classList.remove("show"); }, 2000);
}

// --- STATE MANAGEMENT ---
let currentAction = null;
let targetStudentId = null;

// --- INLINE EDIT TOGGLE ---
function toggleEditMode(isEditing) {
    const viewModes = document.querySelectorAll('.view-mode');
    const editModes = document.querySelectorAll('.edit-mode');
    
    viewModes.forEach(el => el.style.display = isEditing ? 'none' : 'inline-block');
    editModes.forEach(el => el.style.display = isEditing ? 'inline-block' : 'none');
    
    document.getElementById('teacher-view-controls').style.display = isEditing ? 'none' : 'block';
    document.getElementById('teacher-edit-controls').style.display = isEditing ? 'block' : 'none';
}

// --- GENERIC MODAL CONTROLLER ---
function openModal(action, studentId) {
    currentAction = action;
    targetStudentId = studentId;
    
    const modal = document.getElementById('passwordModal');
    const title = document.getElementById('modalTitle');
    const desc = document.getElementById('modalDesc');
    const confirmBtn = document.getElementById('modalConfirmBtn');
    
    if (action === 'delete') {
        title.innerText = 'Xác nhận Xóa';
        title.style.color = '#dc3545';
        desc.innerText = 'Hành động này không thể hoàn tác. Nhập mật khẩu Giáo viên để xác nhận.';
        confirmBtn.className = 'btn btn-danger';
        confirmBtn.style.background = '#dc3545';
    } else if (action === 'edit') {
        title.innerText = 'Lưu thay đổi';
        title.style.color = '#28a745';
        desc.innerText = 'Nhập mật khẩu Giáo viên của bạn để lưu lại thông tin sinh viên này.';
        confirmBtn.className = 'btn';
        confirmBtn.style.background = '#28a745';
    } else if (action === 'create') {
        title.innerText = 'Tạo Sinh Viên';
        title.style.color = '#28a745';
        desc.innerText = 'Nhập mật khẩu Giáo viên của bạn để xác nhận thêm sinh viên mới.';
        confirmBtn.className = 'btn';
        confirmBtn.style.background = '#28a745';
    }
    
    modal.style.display = 'flex';
    document.getElementById('modal_password').focus();
}

function closeModal() {
    document.getElementById('passwordModal').style.display = 'none';
    document.getElementById('modal_password').value = '';
    currentAction = null;
    targetStudentId = null;
}

// --- SUBMIT CONTROLLER ---
function submitAction() {
    const password = document.getElementById('modal_password').value;
    if (!password.trim()) {
        showToast("Vui lòng nhập mật khẩu xác nhận!", "error");
        return;
    }

    if (currentAction === 'delete') {
        document.getElementById('delete_student_id').value = targetStudentId;
        document.getElementById('delete_current_password').value = password;
        document.getElementById('deleteStudentForm').submit();
    } else if (currentAction === 'edit') {
        // Form edit đã tự chứa các data sửa (nằm sẵn ngoài màn hình)
        // Chúng ta chỉ việc bơm mật khẩu vào hidden field và submit nó
        document.getElementById('edit_current_password').value = password;
        document.getElementById('editStudentForm').submit();
    } else if (currentAction === 'create') {
        document.getElementById('create_current_password').value = password;
        document.getElementById('createStudentForm').submit();
    }
}

// --- EVENT LISTENERS ---
document.addEventListener('DOMContentLoaded', function() {
    const passInput = document.getElementById('modal_password');
    if (passInput) {
        passInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault(); 
                submitAction();
            }
        });
    }
});