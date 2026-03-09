// --- TOAST UI ---
function showToast(message, type) {
    var container = document.getElementById("toast-container");
    if (!container) return;

    var toast = document.createElement("div");
    
    var baseClasses = "px-4 py-3 rounded-lg shadow-lg text-white font-medium text-sm transition-all duration-300 transform translate-y-4 opacity-0 flex items-center gap-3";
    var typeClasses = type === 'error' ? "bg-red-500" : "bg-green-500";
    
    toast.className = baseClasses + " " + typeClasses;
    
    var icon = document.createElement("span");
    icon.innerHTML = type === 'error' 
        ? '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        : '<svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
        
    var textNode = document.createElement("div");
    textNode.className = "flex-1 break-words max-w-xs";
    textNode.innerText = message;
    
    toast.appendChild(icon);
    toast.appendChild(textNode);
    
    container.appendChild(toast);

    requestAnimationFrame(function() {
        toast.classList.remove("translate-y-4", "opacity-0");
        toast.classList.add("translate-y-0", "opacity-100");
    });

    setTimeout(function() { 
        toast.classList.remove("translate-y-0", "opacity-100");
        toast.classList.add("translate-y-4", "opacity-0");
        setTimeout(function() {
            if (container.contains(toast)) {
                container.removeChild(toast); 
            }
        }, 300);
    }, 3000);
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