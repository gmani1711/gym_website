document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTables for users
    const usersTable = $('#usersTable').DataTable({
        ajax: {
            url: 'backend/admin_api.php?action=getUsers',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }
            },
            { data: 'email' },
            { data: 'phone' },
            { 
                data: 'created_at',
                render: function(data, type, row) {
                    return new Date(data).toLocaleString();
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info view-user" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary edit-user" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-user" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });
    
    // Initialize DataTables for memberships
    const membershipsTable = $('#membershipsTable').DataTable({
        ajax: {
            url: 'backend/admin_api.php?action=getMemberships',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { 
                data: null,
                render: function(data, type, row) {
                    return row.first_name + ' ' + row.last_name;
                }
            },
            { data: 'plan_name' },
            { 
                data: 'price',
                render: function(data, type, row) {
                    return '₹' + parseFloat(data).toFixed(2);
                }
            },
            { 
                data: 'start_date',
                render: function(data, type, row) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'end_date',
                render: function(data, type, row) {
                    return new Date(data).toLocaleDateString();
                }
            },
            { 
                data: 'status',
                render: function(data, type, row) {
                    let badgeClass = 'badge bg-secondary';
                    if (data === 'active') {
                        badgeClass = 'badge bg-success';
                    } else if (data === 'expired') {
                        badgeClass = 'badge bg-danger';
                    } else if (data === 'pending') {
                        badgeClass = 'badge bg-warning';
                    }
                    return `<span class="${badgeClass}">${data.toUpperCase()}</span>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info view-membership" data-id="${row.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary edit-membership" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-membership" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });
    
    // Initialize DataTables for classes
    const classesTable = $('#classesTable').DataTable({
        ajax: {
            url: 'backend/admin_api.php?action=getClasses',
            dataSrc: ''
        },
        columns: [
            { data: 'class_name' },
            { data: 'user_count' },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info view-class-users" data-class="${row.class_name}">
                            <i class="fas fa-users"></i> View Users
                        </button>
                        <button class="btn btn-sm btn-primary edit-class" data-class="${row.class_name}">
                            <i class="fas fa-edit"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[1, 'desc']]
    });
    
    // Initialize DataTables for registration codes
    const codesTable = $('#codesTable').DataTable({
        ajax: {
            url: 'backend/admin_api.php?action=getRegistrationCodes',
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'code' },
            { 
                data: null,
                render: function(data, type, row) {
                    if (row.discount_percentage > 0) {
                        return row.discount_percentage + '%';
                    } else {
                        return '₹' + parseFloat(row.discount_amount).toFixed(2);
                    }
                }
            },
            { 
                data: 'is_used',
                render: function(data, type, row) {
                    return data == 1 ? 
                        '<span class="badge bg-danger">Used</span>' : 
                        '<span class="badge bg-success">Available</span>';
                }
            },
            { 
                data: 'used_by',
                render: function(data, type, row) {
                    return data ? data : 'N/A';
                }
            },
            { 
                data: 'used_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleString() : 'N/A';
                }
            },
            { 
                data: 'expiry_date',
                render: function(data, type, row) {
                    const expiryDate = new Date(data);
                    const today = new Date();
                    let badgeClass = 'badge bg-success';
                    
                    if (expiryDate < today) {
                        badgeClass = 'badge bg-danger';
                    } else if ((expiryDate - today) / (1000 * 60 * 60 * 24) < 30) {
                        badgeClass = 'badge bg-warning';
                    }
                    
                    return `<span class="${badgeClass}">${expiryDate.toLocaleDateString()}</span>`;
                }
            },
            {
                data: null,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary edit-code" data-id="${row.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-code" data-id="${row.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        order: [[0, 'desc']]
    });
    
    // Add Registration Code Form Submission
    $('#addCodeForm').on('submit', function(e) {
        e.preventDefault();
        
        const codeValue = 'FIT-' + $('#code').val();
        const discountType = $('#discountType').val();
        const discountPercentage = discountType === 'percentage' ? $('#discountPercentage').val() : 0;
        const discountAmount = discountType === 'amount' ? $('#discountAmount').val() : 0;
        const expiryDate = $('#expiryDate').val();
        
        $.ajax({
            url: 'backend/admin_api.php?action=addRegistrationCode',
            type: 'POST',
            data: {
                code: codeValue,
                discount_percentage: discountPercentage,
                discount_amount: discountAmount,
                expiry_date: expiryDate
            },
            success: function(response) {
                alert('Registration code added successfully!');
                $('#addCodeModal').modal('hide');
                codesTable.ajax.reload();
            },
            error: function(xhr, status, error) {
                alert('Error adding registration code: ' + xhr.responseText);
            }
        });
    });
    
    // Settings Form Submission
    $('#settingsForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            site_name: $('#siteName').val(),
            contact_email: $('#contactEmail').val(),
            contact_phone: $('#contactPhone').val(),
            address: $('#address').val()
        };
        
        $.ajax({
            url: 'backend/admin_api.php?action=updateSettings',
            type: 'POST',
            data: formData,
            success: function(response) {
                alert('Settings updated successfully!');
            },
            error: function(xhr, status, error) {
                alert('Error updating settings: ' + xhr.responseText);
            }
        });
    });
    
    // Event handlers for user actions
    $(document).on('click', '.view-user', function() {
        const userId = $(this).data('id');
        // Implement view user details functionality
        alert('View user with ID: ' + userId);
    });
    
    $(document).on('click', '.edit-user', function() {
        const userId = $(this).data('id');
        // Implement edit user functionality
        alert('Edit user with ID: ' + userId);
    });
    
    $(document).on('click', '.delete-user', function() {
        const userId = $(this).data('id');
        if (confirm('Are you sure you want to delete this user?')) {
            $.ajax({
                url: 'backend/admin_api.php?action=deleteUser',
                type: 'POST',
                data: { id: userId },
                success: function(response) {
                    alert('User deleted successfully!');
                    usersTable.ajax.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error deleting user: ' + xhr.responseText);
                }
            });
        }
    });
    
    // Event handlers for membership actions
    $(document).on('click', '.view-membership', function() {
        const membershipId = $(this).data('id');
        // Implement view membership details functionality
        alert('View membership with ID: ' + membershipId);
    });
    
    $(document).on('click', '.edit-membership', function() {
        const membershipId = $(this).data('id');
        // Implement edit membership functionality
        alert('Edit membership with ID: ' + membershipId);
    });
    
    $(document).on('click', '.delete-membership', function() {
        const membershipId = $(this).data('id');
        