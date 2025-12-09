/**
 * Products Management - Full AJAX CRUD
 * Enhanced product management with real-time updates, status toggle, and featured toggle
 */

class ProductsManagement {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.setupFormHandlers();
    }

    bindEvents() {
        // Toggle status buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('toggle-status-btn')) {
                e.preventDefault();
                this.toggleStatus(e.target);
            }
        });

        // Toggle featured buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('toggle-featured-btn')) {
                e.preventDefault();
                this.toggleFeatured(e.target);
            }
        });

        // Delete buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('delete-product-btn')) {
                e.preventDefault();
                this.deleteProduct(e.target);
            }
        });

        // Quick edit buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-edit-btn')) {
                e.preventDefault();
                this.quickEdit(e.target);
            }
        });
    }

    setupFormHandlers() {
        // Handle AJAX form submissions
        const forms = document.querySelectorAll('.ajax-form');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit(form);
            });
        });
    }

    async toggleStatus(button) {
        const productId = button.dataset.productId;
        const currentStatus = button.dataset.status === 'true';
        
        try {
            button.disabled = true;
            button.innerHTML = '<span class="animate-spin">⟳</span> Updating...';

            const response = await fetch(`/admin/products/ajax/${productId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateStatusUI(button, !currentStatus);
                this.showNotification(data.message, 'success');
            } else {
                throw new Error(data.message || 'Failed to update status');
            }
        } catch (error) {
            console.error('Error toggling status:', error);
            this.showNotification('Failed to update product status', 'error');
        } finally {
            button.disabled = false;
        }
    }

    async toggleFeatured(button) {
        const productId = button.dataset.productId;
        const currentFeatured = button.dataset.featured === 'true';
        
        try {
            button.disabled = true;
            const originalContent = button.innerHTML;
            button.innerHTML = '<span class="animate-spin">⟳</span>';

            const response = await fetch(`/admin/products/ajax/${productId}/toggle-featured`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateFeaturedUI(button, !currentFeatured);
                this.showNotification(data.message, 'success');
            } else {
                throw new Error(data.message || 'Failed to update featured status');
            }
        } catch (error) {
            console.error('Error toggling featured:', error);
            this.showNotification('Failed to update featured status', 'error');
        } finally {
            button.disabled = false;
        }
    }

    updateStatusUI(button, isActive) {
        const statusBadge = button.closest('tr').querySelector('.status-badge');
        const newStatus = isActive ? 'Active' : 'Inactive';
        const newClass = isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        
        statusBadge.textContent = newStatus;
        statusBadge.className = `status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${newClass}`;
        
        button.dataset.status = isActive;
        button.textContent = isActive ? 'Deactivate' : 'Activate';
    }

    updateFeaturedUI(button, isFeatured) {
        const row = button.closest('tr');
        const featuredBadge = row.querySelector('.featured-badge');
        
        if (isFeatured) {
            if (!featuredBadge) {
                const productInfo = row.querySelector('.flex.items-center.space-x-3 div');
                const badge = document.createElement('span');
                badge.className = 'featured-badge inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800';
                badge.textContent = 'Featured';
                productInfo.appendChild(badge);
            }
        } else {
            if (featuredBadge) {
                featuredBadge.remove();
            }
        }
        
        button.dataset.featured = isFeatured;
        button.title = isFeatured ? 'Remove from featured' : 'Add to featured';
        
        // Update star icon
        const starIcon = button.querySelector('svg');
        if (isFeatured) {
            starIcon.setAttribute('fill', 'currentColor');
        } else {
            starIcon.setAttribute('fill', 'none');
        }
    }

    async deleteProduct(button) {
        const productId = button.dataset.productId;
        const productName = button.dataset.productName;
        
        if (!confirm(`Are you sure you want to delete "${productName}"? This will also delete all its samples.`)) {
            return;
        }

        try {
            button.disabled = true;
            button.innerHTML = '<span class="animate-spin">⟳</span> Deleting...';

            const response = await fetch(`/admin/products/ajax/${productId}/destroy`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });

            const data = await response.json();

            if (data.success) {
                this.removeProductRow(button);
                this.showNotification(data.message, 'success');
            } else {
                throw new Error(data.message || 'Failed to delete product');
            }
        } catch (error) {
            console.error('Error deleting product:', error);
            this.showNotification('Failed to delete product', 'error');
        } finally {
            button.disabled = false;
        }
    }

    removeProductRow(button) {
        const row = button.closest('tr');
        row.style.transition = 'opacity 0.3s ease';
        row.style.opacity = '0';
        
        setTimeout(() => {
            row.remove();
            this.checkEmptyState();
        }, 300);
    }

    checkEmptyState() {
        const tbody = document.querySelector('tbody');
        const rows = tbody.querySelectorAll('tr');
        
        if (rows.length === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                    <div class="space-y-3">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m14 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m14 0H6m0 0l4-4m0 0l4 4m-4-4v12"></path>
                        </svg>
                        <p class="text-lg font-medium">No products found</p>
                        <p>Get started by creating your first product.</p>
                        <a href="/admin/products/create" class="btn-primary">Create Product</a>
                    </div>
                </td>
            `;
            tbody.appendChild(emptyRow);
        }
    }

    async handleFormSubmit(form) {
        const formData = new FormData(form);
        const isEdit = form.dataset.action === 'edit';
        const productId = form.dataset.productId;
        
        try {
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.textContent;
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="animate-spin">⟳</span> Saving...';

            const url = isEdit 
                ? `/admin/products/ajax/${productId}/update`
                : '/admin/products/ajax/store';
            
            const method = isEdit ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                this.showNotification(data.message, 'success');
                
                if (!isEdit) {
                    // Add new product to table
                    this.addProductToTable(data.product);
                    form.reset();
                } else {
                    // Update existing product in table
                    this.updateProductInTable(data.product);
                }
            } else {
                throw new Error(data.message || 'Failed to save product');
            }
        } catch (error) {
            console.error('Error saving product:', error);
            this.showNotification('Failed to save product', 'error');
        } finally {
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = false;
            submitButton.textContent = submitButton.dataset.originalText || 'Save';
        }
    }

    addProductToTable(product) {
        const tbody = document.querySelector('tbody');
        const emptyRow = tbody.querySelector('td[colspan="6"]');
        if (emptyRow) {
            emptyRow.closest('tr').remove();
        }

        const newRow = this.createProductRow(product);
        tbody.insertBefore(newRow, tbody.firstChild);
    }

    updateProductInTable(product) {
        const row = document.querySelector(`tr[data-product-id="${product.id}"]`);
        if (row) {
            const updatedRow = this.createProductRow(product);
            row.parentNode.replaceChild(updatedRow, row);
        }
    }

    createProductRow(product) {
        const row = document.createElement('tr');
        row.dataset.productId = product.id;
        row.className = 'hover:bg-gray-50';
        
        const statusClass = product.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const statusText = product.is_active ? 'Active' : 'Inactive';
        const featuredBadge = product.is_featured ? '<span class="featured-badge inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Featured</span>' : '';
        const starFill = product.is_featured ? 'currentColor' : 'none';
        
        row.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center space-x-3">
                    ${product.image_path ? 
                        `<img src="${product.image_path}" alt="${product.title}" class="w-12 h-12 rounded-lg object-cover">` :
                        `<div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>`
                    }
                    <div>
                        <p class="font-medium text-gray-900">${product.title}</p>
                        <p class="text-sm text-gray-500">${product.slug}</p>
                        ${featuredBadge}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                    ${product.category || 'Uncategorized'}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">
                    ${product.price_display || `PKR ${parseFloat(product.price).toFixed(0)}`}
                </div>
                ${product.price_display && product.price ? 
                    `<div class="text-xs text-gray-500">Base: PKR ${parseFloat(product.price).toFixed(0)}</div>` : ''
                }
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900">${product.samples ? product.samples.length : 0}</div>
                <div class="text-xs text-gray-500">
                    ${product.samples && product.samples.filter(s => s.is_active).length > 0 ? 
                        `${product.samples.filter(s => s.is_active).length} active` : 
                        'No samples'
                    }
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="status-badge inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex items-center justify-end space-x-2">
                    <a href="/services/${product.slug}" 
                       class="text-blue-600 hover:text-blue-900" 
                       target="_blank" 
                       title="View on site">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                    <a href="/admin/products/${product.id}/edit" 
                       class="text-indigo-600 hover:text-indigo-900">Edit</a>
                    <button class="toggle-featured-btn text-yellow-600 hover:text-yellow-900" 
                            data-product-id="${product.id}" 
                            data-featured="${product.is_featured}"
                            title="${product.is_featured ? 'Remove from featured' : 'Add to featured'}">
                        <svg class="w-4 h-4" fill="${starFill}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </button>
                    <button class="toggle-status-btn text-yellow-600 hover:text-yellow-900" 
                            data-product-id="${product.id}" 
                            data-status="${product.is_active}">
                        ${product.is_active ? 'Deactivate' : 'Activate'}
                    </button>
                    <button class="delete-product-btn text-red-600 hover:text-red-900" 
                            data-product-id="${product.id}" 
                            data-product-name="${product.title}">Delete</button>
                </div>
            </td>
        `;
        
        return row;
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.products-notification');
        existingNotifications.forEach(notification => notification.remove());

        const notification = document.createElement('div');
        notification.className = `products-notification fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                        type === 'error' ?
                        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>' :
                        '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
                <div class="ml-auto pl-3">
                    <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.transition = 'opacity 0.3s ease';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new ProductsManagement();
});
