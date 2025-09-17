/**
 * Unified Cart Functions
 * This file provides a single, consistent cart API for all add-to-cart buttons
 */

// Global cart functions
function getLocalCart() {
    try {
        return JSON.parse(localStorage.getItem('cart') || '[]');
    } catch (e) {
        console.error('Error parsing cart from localStorage:', e);
        return [];
    }
}

function setLocalCart(items) {
    try {
        localStorage.setItem('cart', JSON.stringify(items));
        updateNavbarCount();
    } catch (e) {
        console.error('Error saving cart to localStorage:', e);
    }
}

function updateNavbarCount() {
    try {
        const items = getLocalCart();
        const count = items.reduce((a, b) => a + (parseInt(b.quantity) || 0), 0);
        const el = document.querySelector('.cart-count');
        if (el) {
            el.textContent = count;
        }
    } catch (e) {
        console.error('Error updating navbar count:', e);
    }
}

async function syncToServer(item) {
    try {
        const response = await fetch('/cart/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Accept': 'application/json',
            },
            body: JSON.stringify(item)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error syncing to server:', error);
        throw error;
    }
}

/**
 * Unified Add to Cart Function
 * This function handles all add-to-cart operations consistently
 */
async function addToCart(serviceId, serviceSampleId = null, quantity = 1, unitPrice = null, customRequirements = '', size = 'standard', paperType = 'standard', finish = 'matte') {
    try {
        // Create cart item object
        const cartItem = {
            service_id: parseInt(serviceId),
            service_sample_id: serviceSampleId ? parseInt(serviceSampleId) : null,
            quantity: parseInt(quantity),
            unit_price: unitPrice ? parseFloat(unitPrice) : null,
            custom_requirements: customRequirements,
            size: size,
            paper_type: paperType,
            finish: finish,
            timestamp: Date.now()
        };

        // Get current cart
        const cart = getLocalCart();
        
        // Check if item already exists (with same variants)
        const existingIndex = cart.findIndex(i => 
            i.service_id === cartItem.service_id &&
            i.service_sample_id === cartItem.service_sample_id &&
            i.size === cartItem.size &&
            i.paper_type === cartItem.paper_type &&
            i.finish === cartItem.finish &&
            i.custom_requirements === cartItem.custom_requirements
        );

        if (existingIndex !== -1) {
            // Update existing item quantity
            cart[existingIndex].quantity += cartItem.quantity;
        } else {
            // Add new item
            cart.push(cartItem);
        }

        // Update local storage
        setLocalCart(cart);

        // Sync to server
        try {
            const serverResponse = await syncToServer(cartItem);
            console.log('Server sync successful:', serverResponse);
        } catch (serverError) {
            console.warn('Server sync failed, but local cart updated:', serverError);
        }

        // Show success message
        showNotification('Item added to cart successfully!', 'success');
        
        return true;
    } catch (error) {
        console.error('Error adding to cart:', error);
        showNotification('Error adding item to cart. Please try again.', 'error');
        return false;
    }
}

/**
 * Remove from Cart Function
 */
async function removeFromCart(serviceId, serviceSampleId = null, size = 'standard', paperType = 'standard', finish = 'matte') {
    try {
        const cart = getLocalCart();
        const filteredCart = cart.filter(i => 
            !(i.service_id === parseInt(serviceId) &&
              i.service_sample_id === (serviceSampleId ? parseInt(serviceSampleId) : null) &&
              i.size === size &&
              i.paper_type === paperType &&
              i.finish === finish)
        );
        
        setLocalCart(filteredCart);
        showNotification('Item removed from cart!', 'success');
        return true;
    } catch (error) {
        console.error('Error removing from cart:', error);
        showNotification('Error removing item from cart.', 'error');
        return false;
    }
}

/**
 * Update Cart Item Quantity
 */
async function updateCartQuantity(serviceId, serviceSampleId = null, quantity, size = 'standard', paperType = 'standard', finish = 'matte') {
    try {
        const cart = getLocalCart();
        const itemIndex = cart.findIndex(i => 
            i.service_id === parseInt(serviceId) &&
            i.service_sample_id === (serviceSampleId ? parseInt(serviceSampleId) : null) &&
            i.size === size &&
            i.paper_type === paperType &&
            i.finish === finish
        );

        if (itemIndex !== -1) {
            if (quantity <= 0) {
                cart.splice(itemIndex, 1);
            } else {
                cart[itemIndex].quantity = parseInt(quantity);
            }
            setLocalCart(cart);
        }
        
        return true;
    } catch (error) {
        console.error('Error updating cart quantity:', error);
        return false;
    }
}

/**
 * Show Notification
 */
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 rounded-lg px-6 py-3 text-white font-medium shadow-lg transition-all ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

/**
 * Initialize Cart Event Listeners
 * This function sets up all cart-related event listeners
 */
function initializeCartListeners() {
    // Add to cart buttons (class-based)
    document.addEventListener('click', async function(e) {
        if (e.target.classList.contains('add-to-cart-btn')) {
            e.preventDefault();
            const btn = e.target;
            
            const serviceId = btn.dataset.serviceId;
            const sampleId = btn.dataset.sampleId;
            const quantity = btn.dataset.quantity || 1;
            const unitPrice = btn.dataset.unitPrice;
            const customRequirements = btn.dataset.customRequirements || '';
            const size = btn.dataset.size || 'standard';
            const paperType = btn.dataset.paperType || 'standard';
            const finish = btn.dataset.finish || 'matte';
            
            if (!serviceId) {
                showNotification('Error: Service ID not found', 'error');
                return;
            }
            
            // Disable button temporarily
            btn.disabled = true;
            btn.textContent = 'Adding...';
            
            const success = await addToCart(serviceId, sampleId, quantity, unitPrice, customRequirements, size, paperType, finish);
            
            // Re-enable button
            btn.disabled = false;
            btn.textContent = btn.dataset.originalText || 'Add to Cart';
            
            if (success) {
                // Update button state
                btn.classList.add('bg-green-600');
                setTimeout(() => {
                    btn.classList.remove('bg-green-600');
                }, 2000);
            }
        }
    });

    // Add sample to cart (ID-based)
    document.addEventListener('click', async function(e) {
        if (e.target.id === 'addSampleToCart') {
            e.preventDefault();
            const btn = e.target;
            
            const serviceId = btn.dataset.serviceId;
            const sampleId = btn.dataset.sampleId;
            const unitPrice = btn.dataset.unitPrice;
            const quantity = document.getElementById('quantity')?.value || 1;
            const size = document.getElementById('size')?.value || 'standard';
            const paperType = document.getElementById('paperType')?.value || 'standard';
            const finish = document.getElementById('finish')?.value || 'matte';
            const customRequirements = document.getElementById('customRequirements')?.value || '';
            
            if (!serviceId) {
                showNotification('Error: Service ID not found', 'error');
                return;
            }
            
            // Disable button temporarily
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Adding...';
            
            const success = await addToCart(serviceId, sampleId, quantity, unitPrice, customRequirements, size, paperType, finish);
            
            // Re-enable button
            btn.disabled = false;
            btn.textContent = originalText;
            
            if (success) {
                // Update button state
                btn.classList.add('bg-green-600');
                setTimeout(() => {
                    btn.classList.remove('bg-green-600');
                }, 2000);
            }
        }
    });

    // Modal add to cart (ID-based)
    document.addEventListener('click', async function(e) {
        if (e.target.id === 'modalAddToCart') {
            e.preventDefault();
            const btn = e.target;
            
            if (!window.currentProduct) {
                showNotification('Error: No product selected', 'error');
                return;
            }
            
            const serviceId = window.currentProduct.serviceId;
            const sampleId = window.currentProduct.sampleId;
            const quantity = document.getElementById('modalQuantity')?.value || 1;
            const unitPrice = window.currentProduct.unitPrice;
            const size = window.currentProduct.selectedVariants?.size || 'standard';
            const paperType = window.currentProduct.selectedVariants?.paperType || 'standard';
            const finish = window.currentProduct.selectedVariants?.finish || 'matte';
            const customRequirements = document.getElementById('modalCustomRequirements')?.value || '';
            
            if (!serviceId) {
                showNotification('Error: Service ID not found', 'error');
                return;
            }
            
            // Disable button temporarily
            btn.disabled = true;
            const originalText = btn.textContent;
            btn.textContent = 'Adding...';
            
            const success = await addToCart(serviceId, sampleId, quantity, unitPrice, customRequirements, size, paperType, finish);
            
            // Re-enable button
            btn.disabled = false;
            btn.textContent = originalText;
            
            if (success) {
                // Update button state
                btn.classList.add('bg-green-600');
                setTimeout(() => {
                    btn.classList.remove('bg-green-600');
                }, 2000);
            }
        }
    });
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeCartListeners();
    updateNavbarCount();
});

// Export functions for global use
window.addToCart = addToCart;
window.removeFromCart = removeFromCart;
window.updateCartQuantity = updateCartQuantity;
window.getLocalCart = getLocalCart;
window.setLocalCart = setLocalCart;
window.updateNavbarCount = updateNavbarCount;
window.syncToServer = syncToServer;
window.showNotification = showNotification;
