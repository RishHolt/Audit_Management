// SweetAlert2 Utility Functions
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: true,
    confirmButtonText: 'OK',
    timer: 2000,
    timerProgressBar: true,
    customClass: {
        popup: 'rounded-lg',
        title: 'text-[#4E3B2A]',
        content: 'text-[#4E3B2A]',
        confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
    }
});

// CSRF Token handling
let csrfToken = '';

function updateCSRFToken(response) {
    const token = response.headers.get('X-CSRF-Token');
    if (token) csrfToken = token;
}

// Loading indicator with custom message and cancelable option
function showLoading(message = 'Please wait...', cancelable = false) {
    return Swal.fire({
        title: message,
        allowOutsideClick: cancelable,
        showCancelButton: cancelable,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        willOpen: () => {
            Swal.showLoading();
        },
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
        }
    });
}

// Close Loading State
function closeLoading() {
    Swal.close();
}

// Success message with refresh
function showSuccessWithRefresh(message = 'Operation successful', timer = 1500) {
    return Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        timer: timer,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            content: 'text-[#4E3B2A]',
            confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
        }
    }).then(() => {
        window.location.reload();
    });
}

// Error message with optional retry callback
function showError(message, retryCallback = null) {
    return Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        showCancelButton: !!retryCallback,
        confirmButtonText: retryCallback ? 'Retry' : 'OK',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            content: 'text-[#4E3B2A]',
            confirmButton: 'bg-accent text-white px-4 py-2 rounded-md',
            cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
        }
    }).then((result) => {
        if (result.isConfirmed && retryCallback) {
            retryCallback();
        }
    });
}

// Delete confirmation with refresh
async function showDeleteConfirmation(onConfirm, itemName = 'item') {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: `This ${itemName} will be permanently deleted.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            content: 'text-[#4E3B2A]',
            confirmButton: 'bg-red-500 text-white px-4 py-2 rounded-md',
            cancelButton: 'bg-gray-400 text-white px-4 py-2 rounded-md'
        }
    });

    if (result.isConfirmed) {
        try {
            await onConfirm();
            await showSuccessWithRefresh('Item deleted successfully');
        } catch (error) {
            await showError(error.message, onConfirm);
        }
    }
}

// Generic AJAX handler with CSRF and error handling
async function handleAjaxRequest(url, options = {}) {
    try {
        // Add CSRF token to headers if available
        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrfToken && { 'X-CSRF-Token': csrfToken }),
            ...options.headers
        };

        const response = await fetch(url, { ...options, headers });
        updateCSRFToken(response);

        const contentType = response.headers.get('content-type');
        let data;

        if (contentType && contentType.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            throw new Error(text || 'Unexpected response from server');
        }

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Operation failed');
        }

        return data;
    } catch (error) {
        console.error('AJAX Error:', error);
        throw error;
    }
}

// Generic CRUD handlers with improved error handling
async function handleCreate(url, formData, modalId = null) {
    const submitBtn = document.querySelector(`#${modalId} button[type="submit"]`);
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Processing...';
    }

    try {
        showLoading('Creating...');
        
        // Add CSRF token to form data
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }

        const data = await handleAjaxRequest(url, {
            method: 'POST',
            body: formData
        });

        // Close modal if provided
        if (modalId) {
            const closeButton = document.querySelector(`#${modalId} [data-modal-hide="${modalId}"]`);
            if (closeButton) {
                closeButton.click();
            }
        }

        await showCreateSuccess(data.message);
    } catch (error) {
        await showError(error.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit';
        }
        closeLoading();
    }
}

// Update handler with similar improvements
async function handleUpdate(url, formData, modalId = null) {
    const submitBtn = document.querySelector(`#${modalId} button[type="submit"]`);
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner"></span> Updating...';
    }

    try {
        showLoading('Updating...');
        
        if (csrfToken) {
            formData.append('csrf_token', csrfToken);
        }

        const data = await handleAjaxRequest(url, {
            method: 'POST',
            body: formData
        });

        if (modalId) {
            const closeButton = document.querySelector(`#${modalId} [data-modal-hide="${modalId}"]`);
            if (closeButton) {
                closeButton.click();
            }
        }

        await showUpdateSuccess(data.message);
    } catch (error) {
        await showError(error.message);
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Save Changes';
        }
        closeLoading();
    }
}

// Delete handler with improved error handling
async function handleDelete(url, itemName = 'item') {
    await showDeleteConfirmation(
        async () => {
            showLoading('Deleting...');
            await handleAjaxRequest(url);
        },
        itemName
    );
}

// Create success with refresh
function showCreateSuccess(message = 'Item created successfully') {
    return Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            content: 'text-[#4E3B2A]',
            confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
        }
    }).then(() => {
        window.location.reload();
    });
}

// Update success with refresh
function showUpdateSuccess(message = 'Changes saved successfully') {
    return Swal.fire({
        icon: 'success',
        title: 'Success',
        text: message,
        showConfirmButton: true,
        confirmButtonText: 'OK',
        customClass: {
            popup: 'rounded-lg',
            title: 'text-[#4E3B2A]',
            content: 'text-[#4E3B2A]',
            confirmButton: 'bg-accent text-white px-4 py-2 rounded-md'
        }
    }).then(() => {
        window.location.reload();
    });
}

// Export all functions to window object
window.showLoading = showLoading;
window.closeLoading = closeLoading;
window.showSuccessWithRefresh = showSuccessWithRefresh;
window.showError = showError;
window.showDeleteConfirmation = showDeleteConfirmation;
window.showCreateSuccess = showCreateSuccess;
window.showUpdateSuccess = showUpdateSuccess;
window.handleCreate = handleCreate;
window.handleUpdate = handleUpdate;
window.handleDelete = handleDelete;
window.handleAjaxRequest = handleAjaxRequest; 