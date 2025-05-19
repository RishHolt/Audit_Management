// SweetAlert2 Utility Functions
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});

// Create Success Notification
const showCreateSuccess = (customMessage = 'The item has been created.') => {
    Toast.fire({
        icon: 'success',
        title: 'Created Successfully',
        text: customMessage
    });
};

// Update Success Notification
const showUpdateSuccess = (customMessage = 'The changes have been saved.') => {
    Toast.fire({
        icon: 'success',
        title: 'Updated Successfully',
        text: customMessage
    });
};

// Delete Confirmation Dialog
const showDeleteConfirmation = async (deleteCallback, itemName = 'item') => {
    try {
        const result = await Swal.fire({
            title: 'Are you sure?',
            text: "This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        });

        if (result.isConfirmed) {
            await deleteCallback();
            await Swal.fire({
                title: 'Deleted!',
                text: `The ${itemName} has been removed.`,
                icon: 'success',
                timer: 2000
            });
            return true;
        }
        return false;
    } catch (error) {
        Swal.fire({
            title: 'Error',
            text: 'An error occurred while deleting the item.',
            icon: 'error'
        });
        console.error('Delete operation failed:', error);
        return false;
    }
};

// Error Notification
const showError = (message = 'An error occurred.') => {
    Swal.fire({
        title: 'Error',
        text: message,
        icon: 'error',
        confirmButtonColor: '#3085d6'
    });
};

// Loading State
const showLoading = (message = 'Processing...') => {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
};

// Close Loading State
const closeLoading = () => {
    Swal.close();
}; 