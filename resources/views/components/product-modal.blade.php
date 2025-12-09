<!-- Product Modal Component -->
<div id="productModal" 
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4 backdrop-blur-sm"
     role="dialog"
     aria-labelledby="productModalTitle"
     aria-modal="true"
     x-data="productModal()"
     x-show="isOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape="closeModal()"
     @click.self="closeModal()">
    
    <div class="relative max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl"
         x-transition:enter="transition ease-out duration-200 transform"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Close Button -->
        <button type="button" 
                class="absolute right-4 top-4 z-10 rounded-full bg-white/80 p-2 text-gray-600 transition-colors hover:bg-white hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2"
                @click="closeModal()"
                aria-label="Close modal">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Modal Content -->
        <div class="flex flex-col lg:flex-row">
            <!-- Product Image Section -->
            <div class="flex-1 bg-gradient-to-br from-slate-50 to-white p-4 lg:p-6">
                <!-- Main Image -->
                <div class="relative max-w-md mx-auto overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-slate-200">
                    <div class="aspect-[4/3] w-full">
                        <img id="modalProductImage" 
                             src="" 
                             alt=""
                             class="h-full w-full object-contain bg-white"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
                        <div class="hidden h-full w-full items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-sm text-slate-600">
                            <div class="text-center p-6">
                                <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="font-medium">Image not available</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sample Thumbnails -->
                <div class="mt-4 grid grid-cols-4 gap-2 max-w-md mx-auto">
                    <!-- Main image thumbnail -->
                    <button class="gallery-thumb aspect-square overflow-hidden rounded-md ring-2 ring-[--color-brand-blue] hover:ring-[--color-brand-orange] transition-all" 
                            onclick="changeModalImage(this.querySelector('img').src)"
                            aria-label="View main image">
                        <img id="modalMainThumb" src="" alt="Main view" class="h-full w-full object-cover" />
                    </button>
                    
                    <!-- Additional view thumbnails -->
                    <button class="gallery-thumb aspect-square overflow-hidden rounded-md ring-1 ring-slate-200 hover:ring-2 hover:ring-[--color-brand-blue] transition-all bg-gradient-to-br from-slate-100 to-slate-200" 
                            onclick="changeModalImage(document.getElementById('modalProductImage').src)"
                            aria-label="View alternate angle">
                        <div class="h-full w-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </button>
                    
                    <button class="gallery-thumb aspect-square overflow-hidden rounded-md ring-1 ring-slate-200 hover:ring-2 hover:ring-[--color-brand-blue] transition-all bg-gradient-to-br from-slate-100 to-slate-200"
                            aria-label="View product details">
                        <div class="h-full w-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </button>
                    
                    <button class="gallery-thumb aspect-square overflow-hidden rounded-md ring-1 ring-slate-200 hover:ring-2 hover:ring-[--color-brand-blue] transition-all bg-gradient-to-br from-slate-100 to-slate-200"
                            aria-label="View size guide">
                        <div class="h-full w-full flex items-center justify-center">
                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zM21 5a2 2 0 00-2-2h-4a2 2 0 00-2 2v12a4 4 0 004 4h4a2 2 0 002-2V5z"/>
                            </svg>
                        </div>
                    </button>
                </div>
                
                <!-- Thumbnail Gallery -->
                <div id="modalGallery" class="mt-3 grid grid-cols-4 gap-2">
                    <!-- Main image thumbnail (always visible) -->
                    <button type="button" 
                            id="mainImageThumb"
                            class="thumbnail-btn aspect-square overflow-hidden rounded-md ring-2 ring-[--color-brand-blue] transition-all hover:ring-[--color-brand-orange] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2"
                            onclick="changeModalImage(this.dataset.imageSrc)"
                            aria-label="View main product image">
                        <img src="" 
                             alt="Main product view"
                             class="h-full w-full object-cover"
                             loading="lazy" />
                    </button>
                    
                    <!-- Additional thumbnails (hidden by default, shown if available) -->
                    <button type="button" 
                            class="thumbnail-btn aspect-square overflow-hidden rounded-md ring-1 ring-gray-200 transition-all hover:ring-2 hover:ring-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2 hidden"
                            onclick="changeModalImage(this.dataset.imageSrc)"
                            aria-label="Alternative product view">
                        <img src="/images/placeholder-1.jpg" 
                             alt="Alternative view 1"
                             class="h-full w-full object-cover"
                             loading="lazy"
                             onerror="this.parentElement.style.display='none'" />
                    </button>
                    
                    <button type="button" 
                            class="thumbnail-btn aspect-square overflow-hidden rounded-md ring-1 ring-gray-200 transition-all hover:ring-2 hover:ring-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2 hidden"
                            onclick="changeModalImage(this.dataset.imageSrc)"
                            aria-label="Alternative product view">
                        <img src="/images/placeholder-2.jpg" 
                             alt="Alternative view 2"
                             class="h-full w-full object-cover"
                             loading="lazy"
                             onerror="this.parentElement.style.display='none'" />
                    </button>
                    
                    <button type="button" 
                            class="thumbnail-btn aspect-square overflow-hidden rounded-md ring-1 ring-gray-200 transition-all hover:ring-2 hover:ring-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2 hidden"
                            onclick="changeModalImage(this.dataset.imageSrc)"
                            aria-label="Alternative product view">
                        <img src="/images/placeholder-3.jpg" 
                             alt="Alternative view 3"
                             class="h-full w-full object-cover"
                             loading="lazy"
                             onerror="this.parentElement.style.display='none'" />
                    </button>
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="flex-1 p-6 lg:p-8">
                <div class="flex h-full flex-col">
                    <!-- Header -->
                    <div class="mb-6">
                        <div class="mb-2 flex items-center gap-2">
                            <span id="modalBadges" class="hidden"></span>
                        </div>
                        <h2 id="productModalTitle" class="text-2xl font-bold text-[--color-brand-deepblue] lg:text-3xl"></h2>
                        <p id="modalServiceName" class="text-lg text-[--color-brand-blue]"></p>
                        <p id="modalDescription" class="mt-3 text-gray-600"></p>
                    </div>

                    <!-- Options Section -->
                    <div class="flex-1 space-y-6">
                        <!-- Quantity -->
                        <div>
                            <label for="modalQuantity" class="block text-sm font-semibold text-gray-700 mb-2">Quantity</label>
                            <div class="flex items-center gap-3">
                                <button type="button" 
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 transition-colors hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                                        @click="decreaseQuantity()"
                                        :disabled="quantity <= 1"
                                        aria-label="Decrease quantity">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <input type="number" 
                                       id="modalQuantity"
                                       x-model.number="quantity"
                                       min="1"
                                       max="999"
                                       class="w-20 rounded-lg border border-gray-300 px-3 py-2 text-center text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                                       @input="validateQuantity()">
                                <button type="button" 
                                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 text-gray-600 transition-colors hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                                        @click="increaseQuantity()"
                                        :disabled="quantity >= 999"
                                        aria-label="Increase quantity">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Size/Format Options -->
                        <div id="modalSizeOptions" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Size/Format</label>
                            <div class="grid grid-cols-2 gap-2" id="sizeOptionsContainer">
                                <!-- Options will be dynamically populated -->
                            </div>
                        </div>

                        <!-- Color/Finish Options -->
                        <div id="modalColorOptions" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Color/Finish</label>
                            <div class="flex flex-wrap gap-2" id="colorOptionsContainer">
                                <!-- Options will be dynamically populated -->
                            </div>
                        </div>

                        <!-- Personalization Text -->
                        <div id="modalPersonalization" class="hidden">
                            <label for="personalizationText" class="block text-sm font-semibold text-gray-700 mb-2">
                                Personalization Text
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea id="personalizationText"
                                     x-model="personalizationText"
                                     rows="3"
                                     maxlength="200"
                                     placeholder="Enter your custom text here..."
                                     class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-[--color-brand-blue] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue]"
                                     @input="validatePersonalization()"></textarea>
                            <div class="mt-1 flex justify-between">
                                <span id="personalizationError" class="text-sm text-red-500 hidden">This field is required</span>
                                <span class="text-xs text-gray-500" x-text="`${personalizationText.length}/200`"></span>
                            </div>
                        </div>

                        <!-- File Upload -->
                        <div id="modalFileUpload" class="hidden">
                            <label for="artworkFile" class="block text-sm font-semibold text-gray-700 mb-2">
                                Upload Artwork
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="rounded-lg border-2 border-dashed border-gray-300 p-6 transition-colors hover:border-[--color-brand-blue]">
                                <input type="file" 
                                       id="artworkFile"
                                       x-ref="fileInput"
                                       accept=".jpg,.jpeg,.png,.pdf,.ai,.psd"
                                       class="hidden"
                                       @change="handleFileUpload($event)">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                    </svg>
                                    <div class="mt-4">
                                        <button type="button" 
                                                class="text-[--color-brand-blue] hover:text-[--color-brand-orange] font-medium"
                                                @click="$refs.fileInput.click()">
                                            Choose file
                                        </button>
                                        <span class="text-gray-500">or drag and drop</span>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">JPG, PNG, PDF, AI, PSD up to 10MB</p>
                                </div>
                                <div id="fileInfo" class="mt-4 hidden">
                                    <div class="flex items-center justify-between rounded-lg bg-gray-50 p-3">
                                        <div class="flex items-center gap-2" x-show="selectedFile !== null">
                                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium" x-text="selectedFile && selectedFile.name ? selectedFile.name : 'No file selected'"></span>
                                        </div>
                                        <button type="button" 
                                                class="text-red-500 hover:text-red-700"
                                                @click="removeFile()"
                                                aria-label="Remove file">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div id="fileError" class="mt-2 text-sm text-red-500 hidden"></div>
                            </div>
                        </div>

                        <!-- Extra Options Checkboxes -->
                        <div id="modalExtras" class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Additional Options</label>
                            <div class="space-y-3" id="extrasContainer">
                                <!-- Checkboxes will be dynamically populated -->
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary and Actions -->
                    <div class="border-t pt-6">
                        <!-- Price Summary -->
                        <div class="mb-4 rounded-lg bg-slate-50 p-4">
                            <div class="flex items-center justify-between text-sm">
                                <span>Unit Price:</span>
                                <span id="modalUnitPrice" class="font-medium"></span>
                            </div>
                            <div class="flex items-center justify-between text-sm" x-show="quantity > 1">
                                <span>Quantity:</span>
                                <span x-text="quantity" class="font-medium"></span>
                            </div>
                            <div class="mt-2 border-t pt-2">
                                <div class="flex items-center justify-between text-lg font-bold text-[--color-brand-deepblue]">
                                    <span>Total:</span>
                                    <span id="modalTotalPrice"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Management Section -->
                        <div x-show="cartItemExists" class="mb-4 rounded-lg bg-blue-50 p-4 border border-blue-200">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-blue-800">Already in Cart</h4>
                                <span class="text-xs text-blue-600" x-text="`${cartItemQuantity} in cart`"></span>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <!-- Quantity Controls -->
                                <div class="flex items-center gap-2">
                                    <button type="button" 
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-blue-300 text-blue-600 transition-colors hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            @click="updateCartQuantity(cartItemQuantity - 1)"
                                            :disabled="isUpdatingCart || cartItemQuantity <= 1"
                                            aria-label="Decrease cart quantity">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                        </svg>
                                    </button>
                                    <span class="text-sm font-medium text-blue-800 min-w-[2rem] text-center" x-text="cartItemQuantity"></span>
                                    <button type="button" 
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-blue-300 text-blue-600 transition-colors hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            @click="updateCartQuantity(cartItemQuantity + 1)"
                                            :disabled="isUpdatingCart"
                                            aria-label="Increase cart quantity">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <!-- Remove Button -->
                                <button type="button" 
                                        class="flex items-center gap-1 px-3 py-1 text-xs font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
                                        @click="removeFromCart()"
                                        :disabled="isUpdatingCart"
                                        aria-label="Remove from cart">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Remove
                                </button>
                            </div>
                            
                            <!-- Loading State -->
                            <div x-show="isUpdatingCart" class="mt-2 text-xs text-blue-600 flex items-center gap-1">
                                <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                </svg>
                                Updating cart...
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button type="button" 
                                    class="flex-1 rounded-lg border border-gray-300 px-4 py-3 text-center font-medium text-gray-700 transition-colors hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2"
                                    @click="closeModal()">
                                Cancel
                            </button>
                            <button type="button" 
                                    id="addToCartBtn"
                                    class="flex-1 rounded-lg bg-[--color-brand-blue] px-4 py-3 text-center font-semibold text-white transition-colors hover:bg-[--color-brand-orange] focus:outline-none focus:ring-2 focus:ring-[--color-brand-blue] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                                    @click="addToCart()"
                                    :disabled="!isFormValid || isAddingToCart"
                                    aria-label="Add item to cart">
                                <span x-show="!isAddingToCart">Add to Cart</span>
                                <span x-show="isAddingToCart" class="flex items-center justify-center gap-2">
                                    <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to change main modal image
function changeModalImage(imageSrc) {
    const mainImg = document.getElementById('modalProductImage');
    if (mainImg) {
        mainImg.src = imageSrc;
    }
    
    // Update gallery thumb states
    document.querySelectorAll('.gallery-thumb').forEach(thumb => {
        thumb.classList.remove('ring-2', 'ring-[--color-brand-blue]');
        thumb.classList.add('ring-1', 'ring-slate-200');
    });
    
    // Highlight active thumb
    if (event && event.target) {
        const clickedThumb = event.target.closest('.gallery-thumb');
        if (clickedThumb) {
            clickedThumb.classList.remove('ring-1', 'ring-slate-200');
            clickedThumb.classList.add('ring-2', 'ring-[--color-brand-blue]');
        }
    }
}

// Product Modal Alpine.js Component
function productModal() {
    return {
        isOpen: false,
        isAddingToCart: false,
        isUpdatingCart: false,
        currentProduct: null,
        quantity: 1,
        selectedSize: '',
        selectedColor: '',
        personalizationText: '',
        selectedFile: null,
        selectedExtras: [],
        personalizationRequired: false,
        fileRequired: false,
        cartItemExists: false,
        cartItemQuantity: 0,
        cartItemId: null,
        
        init() {
            // Ensure selectedFile is properly initialized
            this.selectedFile = null;
        },
        
        get isFormValid() {
            // Only enforce fields if explicitly required by product options
            if (this.personalizationRequired && !this.personalizationText.trim()) return false;
            if (this.fileRequired && !this.selectedFile) return false;
            return this.quantity >= 1;
        },
        
        // Kept for backward compatibility (not used for validation anymore)
        get isPersonalizationRequired() { return this.personalizationRequired; },
        get isFileRequired() { return this.fileRequired; },

        openModal(productData) {
            this.currentProduct = productData;
            this.resetForm();
            this.populateModal(productData);
            this.isOpen = true;
            
            // Focus management
            this.$nextTick(() => {
                this.trapFocus();
            });
        },

        closeModal() {
            this.isOpen = false;
            this.currentProduct = null;
            this.resetForm();
            
            // Return focus to the trigger element
            if (this.triggerElement) {
                this.triggerElement.focus();
                this.triggerElement = null;
            }
        },

        resetForm() {
            this.quantity = 1;
            this.selectedSize = '';
            this.selectedColor = '';
            this.personalizationText = '';
            this.selectedFile = null;
            this.selectedExtras = [];
            this.isAddingToCart = false;
            this.isUpdatingCart = false;
            this.cartItemExists = false;
            this.cartItemQuantity = 0;
            this.cartItemId = null;
            this.clearErrors();
        },

        populateModal(product) {
            // Set basic product info
            document.getElementById('productModalTitle').textContent = product.title;
            document.getElementById('modalServiceName').textContent = product.serviceName || '';
            document.getElementById('modalDescription').textContent = product.description || '';
            
            // Set main image
            const mainImage = document.getElementById('modalProductImage');
            mainImage.src = product.image;
            mainImage.alt = product.title;
            
            // Set main thumbnail
            const mainThumb = document.getElementById('mainImageThumb');
            mainThumb.dataset.imageSrc = product.image;
            mainThumb.querySelector('img').src = product.image;
            mainThumb.querySelector('img').alt = product.title + ' - main view';
            
            document.getElementById('modalUnitPrice').textContent = product.formattedPrice;
            
            // Set badges
            this.updateBadges(product.badges || []);
            
            // Show/hide options based on product type
            this.toggleOptions(product.options || {});
            
            // Check if item exists in cart
            this.checkCartItemExists();
            
            // Update price
            this.updateTotalPrice();
        },

        checkCartItemExists() {
            if (!this.currentProduct) return;
            
            const cart = this.getLocalCart();
            const customRequirements = this.buildCustomRequirements();
            
            const existingItem = cart.find(item => 
                item.service_id === this.currentProduct.serviceId &&
                item.service_sample_id === (this.currentProduct.sampleId || null) &&
                item.unit_price === this.currentProduct.unitPrice &&
                item.custom_requirements === customRequirements
            );
            
            if (existingItem) {
                this.cartItemExists = true;
                this.cartItemQuantity = existingItem.quantity;
                this.cartItemId = null; // We're working with local cart only
            } else {
                this.cartItemExists = false;
                this.cartItemQuantity = 0;
                this.cartItemId = null;
            }
        },

        updateBadges(badges) {
            const badgesContainer = document.getElementById('modalBadges');
            if (badges.length > 0) {
                badgesContainer.innerHTML = badges.map(badge => 
                    `<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ${this.getBadgeClass(badge.type)}">
                        ${badge.text}
                    </span>`
                ).join('');
                badgesContainer.classList.remove('hidden');
            } else {
                badgesContainer.classList.add('hidden');
            }
        },

        getBadgeClass(type) {
            const classes = {
                'best-seller': 'bg-orange-100 text-orange-800',
                'new': 'bg-green-100 text-green-800',
                'discount': 'bg-red-100 text-red-800'
            };
            return classes[type] || 'bg-gray-100 text-gray-800';
        },

        toggleOptions(options) {
            // Flags (requirements)
            this.personalizationRequired = !!options.personalizationRequired;
            this.fileRequired = !!options.fileRequired;

            // Size options
            if (options.sizes && options.sizes.length > 0) {
                this.populateSizeOptions(options.sizes);
                document.getElementById('modalSizeOptions').classList.remove('hidden');
            } else {
                document.getElementById('modalSizeOptions').classList.add('hidden');
            }

            // Color options
            if (options.colors && options.colors.length > 0) {
                this.populateColorOptions(options.colors);
                document.getElementById('modalColorOptions').classList.remove('hidden');
            } else {
                document.getElementById('modalColorOptions').classList.add('hidden');
            }

            // Personalization
            if (options.personalization) {
                document.getElementById('modalPersonalization').classList.remove('hidden');
            } else {
                document.getElementById('modalPersonalization').classList.add('hidden');
            }

            // File upload
            if (options.fileUpload) {
                document.getElementById('modalFileUpload').classList.remove('hidden');
            } else {
                document.getElementById('modalFileUpload').classList.add('hidden');
            }

            // Extras
            if (options.extras && options.extras.length > 0) {
                this.populateExtras(options.extras);
                document.getElementById('modalExtras').classList.remove('hidden');
            } else {
                document.getElementById('modalExtras').classList.add('hidden');
            }
        },

        populateSizeOptions(sizes) {
            const container = document.getElementById('sizeOptionsContainer');
            container.innerHTML = sizes.map(size => 
                `<label class="flex items-center gap-2 cursor-pointer p-3 border rounded-lg hover:bg-gray-50 transition-colors">
                    <input type="radio" name="size" value="${size.value}" 
                           class="text-[--color-brand-blue] focus:ring-[--color-brand-blue]"
                           @change="selectedSize = '${size.value}'; updateTotalPrice()">
                    <span class="text-sm font-medium">${size.label}</span>
                    ${size.price ? `<span class="text-xs text-gray-500 ml-auto">+${size.price}</span>` : ''}
                </label>`
            ).join('');
        },

        populateColorOptions(colors) {
            const container = document.getElementById('colorOptionsContainer');
            container.innerHTML = colors.map(color => 
                `<label class="flex items-center gap-2 cursor-pointer p-2 border rounded-lg hover:bg-gray-50 transition-colors">
                    <input type="radio" name="color" value="${color.value}"
                           class="text-[--color-brand-blue] focus:ring-[--color-brand-blue]"
                           @change="selectedColor = '${color.value}'; updateTotalPrice()">
                    <span class="text-sm">${color.label}</span>
                </label>`
            ).join('');
        },

        populateExtras(extras) {
            const container = document.getElementById('extrasContainer');
            container.innerHTML = extras.map(extra => 
                `<label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" value="${extra.value}"
                           class="rounded text-[--color-brand-blue] focus:ring-[--color-brand-blue]"
                           @change="toggleExtra('${extra.value}', '${extra.price || 0}')">
                    <span class="flex-1 text-sm font-medium">${extra.label}</span>
                    ${extra.price ? `<span class="text-sm text-gray-500">+${extra.price}</span>` : ''}
                </label>`
            ).join('');
        },

        increaseQuantity() {
            if (this.quantity < 999) {
                this.quantity++;
                this.updateTotalPrice();
            }
        },

        decreaseQuantity() {
            if (this.quantity > 1) {
                this.quantity--;
                this.updateTotalPrice();
            }
        },

        validateQuantity() {
            if (this.quantity < 1) this.quantity = 1;
            if (this.quantity > 999) this.quantity = 999;
            this.updateTotalPrice();
        },

        validatePersonalization() {
            const error = document.getElementById('personalizationError');
            if (this.isPersonalizationRequired && !this.personalizationText.trim()) {
                error.classList.remove('hidden');
            } else {
                error.classList.add('hidden');
            }
        },

        handleFileUpload(event) {
            const file = event.target.files[0];
            const errorElement = document.getElementById('fileError');
            const fileInfo = document.getElementById('fileInfo');
            
            if (!file) {
                this.selectedFile = null;
                return;
            }
            
            // Validate file size (10MB limit)
            if (file.size > 10 * 1024 * 1024) {
                errorElement.textContent = 'File size must be less than 10MB';
                errorElement.classList.remove('hidden');
                this.selectedFile = null;
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!allowedTypes.includes(file.type)) {
                errorElement.textContent = 'Only JPG, PNG, and PDF files are allowed';
                errorElement.classList.remove('hidden');
                this.selectedFile = null;
                return;
            }
            
            this.selectedFile = file;
            fileInfo.classList.remove('hidden');
            errorElement.classList.add('hidden');
        },

        removeFile() {
            this.selectedFile = null;
            document.getElementById('fileInfo').classList.add('hidden');
            this.$refs.fileInput.value = '';
        },

        toggleExtra(value, price) {
            const index = this.selectedExtras.findIndex(extra => extra.value === value);
            if (index > -1) {
                this.selectedExtras.splice(index, 1);
            } else {
                this.selectedExtras.push({ value, price: parseFloat(price) || 0 });
            }
            this.updateTotalPrice();
        },

        updateTotalPrice() {
            if (!this.currentProduct) return;
            
            let total = this.currentProduct.unitPrice * this.quantity;
            
            // Add extras
            this.selectedExtras.forEach(extra => {
                total += extra.price * this.quantity;
            });
            
            document.getElementById('modalTotalPrice').textContent = 'PKR ' + new Intl.NumberFormat().format(total);
        },

        clearErrors() {
            document.getElementById('personalizationError').classList.add('hidden');
            document.getElementById('fileError').classList.add('hidden');
        },

        async addToCart() {
            console.log('Add to Cart clicked');
            console.log('isFormValid:', this.isFormValid);
            console.log('quantity:', this.quantity);
            console.log('personalizationRequired:', this.personalizationRequired);
            console.log('fileRequired:', this.fileRequired);
            console.log('personalizationText:', this.personalizationText);
            console.log('selectedFile:', this.selectedFile);
            
            if (!this.isFormValid) {
                console.log('Form is not valid, returning');
                return;
            }
            this.isAddingToCart = true;
            
            try {
                const cartItem = {
                    service_id: Number(this.currentProduct.serviceId),
                    service_sample_id: this.currentProduct.sampleId ? Number(this.currentProduct.sampleId) : null,
                    quantity: Number(this.quantity) || 1,
                    unit_price: Number(this.currentProduct.unitPrice) || 0,
                    custom_requirements: this.buildCustomRequirements()
                };

                // Optimistic update - add to local cart first
                const cart = this.getLocalCart();
                const existingIndex = cart.findIndex(i => 
                    i.service_id === cartItem.service_id &&
                    i.service_sample_id === cartItem.service_sample_id &&
                    i.unit_price === cartItem.unit_price &&
                    i.custom_requirements === cartItem.custom_requirements
                );

                if (existingIndex !== -1) {
                    cart[existingIndex].quantity += cartItem.quantity;
                } else {
                    cart.push(cartItem);
                }

                this.setLocalCart(cart);
                this.updateCartCount();

                // Sync to server
                const response = await fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(cartItem)
                });

                if (!response.ok) {
                    // Revert optimistic update if server failed
                    if (existingIndex !== -1) {
                        cart[existingIndex].quantity -= cartItem.quantity;
                        if (cart[existingIndex].quantity <= 0) {
                            cart.splice(existingIndex, 1);
                        }
                    } else {
                        cart.pop();
                    }
                    this.setLocalCart(cart);
                    this.updateCartCount();
                    
                    const errorData = await response.json().catch(() => ({ message: 'Server error' }));
                    throw new Error(errorData.message || `Server error (${response.status})`);
                }

                const data = await response.json();
                
                // Show success notification
                this.showToast(data.message || 'Item added to cart successfully!', 'success');
                
                // Update cart count with server response
                if (data.item_count !== undefined) {
                    if (window.CartManager) {
                        window.CartManager.updateCartCount(data.item_count);
                    } else {
                        const cartCountEl = document.querySelector('.cart-count');
                        if (cartCountEl) cartCountEl.textContent = data.item_count;
                    }
                }

                // Refresh cart item state
                this.checkCartItemExists();
                
                // Show cart added indicator
                if (window.CartManager) {
                    window.CartManager.showCartAddedIndicator();
                } else {
                    this.showCartAddedIndicator();
                }
                
                this.closeModal();
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showToast(error.message || 'Error adding item to cart. Please try again.', 'error');
            } finally {
                this.isAddingToCart = false;
            }
        },

        buildCustomRequirements() {
            const requirements = [];
            
            if (this.selectedSize) {
                requirements.push(`Size: ${this.selectedSize}`);
            }
            
            if (this.selectedColor) {
                requirements.push(`Color: ${this.selectedColor}`);
            }
            
            if (this.personalizationText.trim()) {
                requirements.push(`Personalization: ${this.personalizationText.trim()}`);
            }
            
            if (this.selectedExtras.length > 0) {
                const extras = this.selectedExtras.map(extra => extra.value).join(', ');
                requirements.push(`Extras: ${extras}`);
            }
            
            return requirements.join(' | ');
        },

        getLocalCart() {
            try {
                return JSON.parse(localStorage.getItem('cart') || '[]');
            } catch (e) {
                return [];
            }
        },

        setLocalCart(cart) {
            localStorage.setItem('cart', JSON.stringify(cart));
        },

        updateCartCount() {
            try {
                const items = JSON.parse(localStorage.getItem('cart') || '[]');
                const count = items.reduce((a,b)=> a + (parseInt(b.quantity)||0), 0);
                const el = document.querySelector('.cart-count'); 
                if(el) el.textContent = count;
            } catch(e) {}
        },

        showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 translate-x-full opacity-0`;
            
            switch (type) {
                case 'success':
                    toast.classList.add('bg-green-600');
                    break;
                case 'error':
                    toast.classList.add('bg-red-600');
                    break;
                case 'warning':
                    toast.classList.add('bg-yellow-600');
                    break;
                default:
                    toast.classList.add('bg-blue-600');
            }
            
            toast.innerHTML = `
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        ${type === 'success' ? 
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                        }
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            }, 100);
            
            // Auto-hide after 5 seconds (longer for better visibility)
            setTimeout(() => {
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        document.body.removeChild(toast);
                    }
                }, 300);
            }, 5000);
        },

        showCartAddedIndicator() {
            // Animate the cart icon in the navigation
            const cartIcon = document.querySelector('a[href*="cart"]');
            if (cartIcon) {
                cartIcon.classList.add('animate-pulse');
                setTimeout(() => {
                    cartIcon.classList.remove('animate-pulse');
                }, 2000);
            }
            
            // Show a temporary "View Cart" button
            const viewCartBtn = document.createElement('div');
            viewCartBtn.className = 'fixed bottom-4 right-4 z-50';
            viewCartBtn.innerHTML = `
                <a href="{{ route('cart.view') }}" 
                   class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors animate-bounce">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    View Cart
                </a>
            `;
            
            document.body.appendChild(viewCartBtn);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                viewCartBtn.style.opacity = '0';
                viewCartBtn.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    if (viewCartBtn.parentNode) {
                        document.body.removeChild(viewCartBtn);
                    }
                }, 300);
            }, 5000);
        },

        showSuccessMessage() {
            this.showToast('Item added to cart!', 'success');
        },

        async updateCartQuantity(newQuantity) {
            if (newQuantity < 1) return;
            
            this.isUpdatingCart = true;
            
            try {
                // For now, we'll work with local cart only since we don't have server-side cart item IDs
                // The server-side cart will be synced when the user goes to checkout
                const cart = this.getLocalCart();
                const existingIndex = cart.findIndex(item => 
                    item.service_id === this.currentProduct.serviceId &&
                    item.service_sample_id === (this.currentProduct.sampleId || null) &&
                    item.unit_price === this.currentProduct.unitPrice &&
                    item.custom_requirements === this.buildCustomRequirements()
                );
                
                if (existingIndex !== -1) {
                    cart[existingIndex].quantity = newQuantity;
                    this.setLocalCart(cart);
                    this.updateCartCount();
                    this.cartItemQuantity = newQuantity;
                    
                    this.showToast('Cart updated successfully!', 'success');
                } else {
                    this.showToast('Item not found in cart', 'error');
                }
                
            } catch (error) {
                console.error('Error updating cart:', error);
                this.showToast(error.message || 'Error updating cart. Please try again.', 'error');
            } finally {
                this.isUpdatingCart = false;
            }
        },

        async removeFromCart() {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            this.isUpdatingCart = true;
            
            try {
                // Remove from local cart
                const cart = this.getLocalCart();
                const existingIndex = cart.findIndex(item => 
                    item.service_id === this.currentProduct.serviceId &&
                    item.service_sample_id === (this.currentProduct.sampleId || null) &&
                    item.unit_price === this.currentProduct.unitPrice &&
                    item.custom_requirements === this.buildCustomRequirements()
                );
                
                if (existingIndex !== -1) {
                    cart.splice(existingIndex, 1);
                    this.setLocalCart(cart);
                    this.updateCartCount();
                    
                    this.showToast('Item removed from cart!', 'success');
                } else {
                    this.showToast('Item not found in cart', 'error');
                }
                
                // Update cart item state
                this.cartItemExists = false;
                this.cartItemQuantity = 0;
                this.cartItemId = null;
                
            } catch (error) {
                console.error('Error removing from cart:', error);
                this.showToast(error.message || 'Error removing item from cart. Please try again.', 'error');
            } finally {
                this.isUpdatingCart = false;
            }
        },

        trapFocus() {
            const modal = document.getElementById('productModal');
            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];
            
            firstElement?.focus();
            
            modal.addEventListener('keydown', (e) => {
                if (e.key === 'Tab') {
                    if (e.shiftKey && document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    } else if (!e.shiftKey && document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            });
        }
    };
}
</script>
