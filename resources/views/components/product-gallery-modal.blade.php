<!-- Product Gallery Modal -->
<div id="productGalleryModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex min-h-screen items-center justify-center p-4">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeProductGallery()"></div>
        
        <!-- Modal Content -->
        <div class="relative w-full max-w-6xl bg-white rounded-2xl shadow-2xl">
            <!-- Close Button -->
            <button 
                onclick="closeProductGallery()" 
                class="absolute top-4 right-4 z-10 bg-white/90 backdrop-blur-sm rounded-full p-2 hover:bg-white transition-colors"
            >
                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
            
            <div class="grid lg:grid-cols-2 gap-8 p-8">
                <!-- Image Gallery -->
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="relative overflow-hidden rounded-xl bg-slate-100">
                        <img 
                            id="modalMainImage" 
                            src="" 
                            alt="" 
                            class="w-full h-96 object-cover transition-all duration-300"
                        />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    </div>
                    
                    <!-- Thumbnail Gallery -->
                    <div id="modalThumbnails" class="grid grid-cols-4 gap-2">
                        <!-- Thumbnails will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Product Details -->
                <div class="space-y-6">
                    <!-- Product Title & Price -->
                    <div>
                        <h2 id="modalProductTitle" class="text-3xl font-bold text-[--color-brand-deepblue] mb-2"></h2>
                        <div id="modalProductCategory" class="text-lg text-[--color-brand-blue] mb-4"></div>
                        <div id="modalProductPrice" class="text-2xl font-bold text-[--color-brand-deepblue]"></div>
                    </div>
                    
                    <!-- Product Description -->
                    <div id="modalProductDescription" class="text-slate-600">
                        <!-- Description will be populated by JavaScript -->
                    </div>
                    
                    <!-- Variant Selectors -->
                    <div class="space-y-4">
                        <!-- Size Selector -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Size</label>
                            <div class="flex gap-2">
                                <button class="variant-btn active px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="size" data-value="standard">
                                    Standard
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="size" data-value="large">
                                    Large
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="size" data-value="extra-large">
                                    Extra Large
                                </button>
                            </div>
                        </div>
                        
                        <!-- Paper Type Selector -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Paper Type</label>
                            <div class="flex gap-2">
                                <button class="variant-btn active px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="paper" data-value="standard">
                                    Standard
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="paper" data-value="premium">
                                    Premium
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="paper" data-value="luxury">
                                    Luxury
                                </button>
                            </div>
                        </div>
                        
                        <!-- Finish Selector -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Finish</label>
                            <div class="flex gap-2">
                                <button class="variant-btn active px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="finish" data-value="matte">
                                    Matte
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="finish" data-value="glossy">
                                    Glossy
                                </button>
                                <button class="variant-btn px-4 py-2 border border-slate-300 rounded-lg text-sm font-medium hover:border-[--color-brand-blue] hover:text-[--color-brand-blue] transition-colors" data-variant="finish" data-value="satin">
                                    Satin
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity & Add to Cart -->
                    <div class="bg-slate-50 rounded-xl p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <label for="modalQuantity" class="text-sm font-medium text-slate-700">Quantity:</label>
                            <input 
                                type="number" 
                                id="modalQuantity" 
                                min="1" 
                                value="1" 
                                class="w-20 rounded border border-slate-300 px-3 py-2 text-center"
                            >
                        </div>
                        
                        <button 
                            id="modalAddToCart" 
                            class="w-full bg-[--color-brand-blue] text-white py-3 px-6 rounded-lg font-medium hover:bg-[--color-brand-orange] transition-colors"
                        >
                            Add to Cart - <span id="modalTotalPrice"></span>
                        </button>
                        
                        <div class="mt-4 flex gap-2">
                            <a 
                                id="modalViewDetails" 
                                href="#" 
                                class="flex-1 text-center text-[--color-brand-blue] hover:text-[--color-brand-orange] transition-colors py-2 px-4 rounded-lg hover:bg-slate-100"
                            >
                                View Full Details
                            </a>
                            <button 
                                onclick="closeProductGallery()" 
                                class="flex-1 text-center text-slate-600 hover:text-slate-800 transition-colors py-2 px-4 rounded-lg hover:bg-slate-100"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentProduct = null;
let selectedVariants = {
    size: 'standard',
    paper: 'standard',
    finish: 'matte'
};

function openProductGallery(sampleId, serviceId) {
    // Fetch product data (this would typically be an AJAX call)
    // For now, we'll use the data from the page
    const productTile = document.querySelector(`[data-sample-id="${sampleId}"]`).closest('.product-tile');
    const productTitle = productTile.querySelector('h3').textContent;
    const productPrice = productTile.querySelector('.bg-white\\/90').textContent;
    const productImage = productTile.querySelector('img').src;
    const productDescription = productTile.querySelector('p')?.textContent || '';
    
    currentProduct = {
        id: sampleId,
        serviceId: serviceId,
        title: productTitle,
        price: productPrice,
        image: productImage,
        description: productDescription
    };
    
    // Populate modal
    document.getElementById('modalProductTitle').textContent = productTitle;
    document.getElementById('modalProductPrice').textContent = productPrice;
    document.getElementById('modalProductDescription').textContent = productDescription;
    document.getElementById('modalMainImage').src = productImage;
    document.getElementById('modalMainImage').alt = productTitle;
    document.getElementById('modalViewDetails').href = `/services/${serviceId}/samples/${sampleId}`;
    
    // Update total price
    updateTotalPrice();
    
    // Show modal
    document.getElementById('productGalleryModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeProductGallery() {
    document.getElementById('productGalleryModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

function updateTotalPrice() {
    if (!currentProduct) return;
    
    // Calculate price based on variants (simplified)
    const basePrice = parseFloat(currentProduct.price.replace(/[^\d.]/g, ''));
    let multiplier = 1;
    
    if (selectedVariants.size === 'large') multiplier += 0.2;
    if (selectedVariants.size === 'extra-large') multiplier += 0.4;
    if (selectedVariants.paper === 'premium') multiplier += 0.3;
    if (selectedVariants.paper === 'luxury') multiplier += 0.5;
    if (selectedVariants.finish === 'glossy') multiplier += 0.1;
    if (selectedVariants.finish === 'satin') multiplier += 0.2;
    
    const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
    const totalPrice = (basePrice * multiplier * quantity).toFixed(0);
    
    document.getElementById('modalTotalPrice').textContent = `PKR ${totalPrice}`;
}

// Cart functionality
function getLocalCart(){
    try{ 
        return JSON.parse(localStorage.getItem('cart') || '[]');
    }catch(e){ 
        return []; 
    }
}

function setLocalCart(items){ 
    localStorage.setItem('cart', JSON.stringify(items)); 
}

function updateNavbarCount(){
    const items = getLocalCart();
    const count = items.reduce((a,b)=> a + (parseInt(b.quantity)||0), 0);
    const el = document.querySelector('.cart-count'); 
    if(el) {
        el.textContent = count;
    }
}

async function syncToServer(item){
    try{
        const res = await fetch('{{ route('cart.add') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify(item)
        });
        return await res.json();
    }catch(e){ 
        return { success: false }; 
    }
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Variant selection
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('variant-btn')) {
            const variant = e.target.dataset.variant;
            const value = e.target.dataset.value;
            
            // Update active state
            document.querySelectorAll(`[data-variant="${variant}"]`).forEach(btn => {
                btn.classList.remove('active', 'border-[--color-brand-blue]', 'text-[--color-brand-blue]');
                btn.classList.add('border-slate-300');
            });
            e.target.classList.add('active', 'border-[--color-brand-blue]', 'text-[--color-brand-blue]');
            e.target.classList.remove('border-slate-300');
            
            // Update selected variant
            selectedVariants[variant] = value;
            updateTotalPrice();
        }
    });
    
    // Quantity change
    document.getElementById('modalQuantity').addEventListener('input', updateTotalPrice);
    
    // Add to cart from modal
    document.getElementById('modalAddToCart').addEventListener('click', function() {
        if (!currentProduct) return;
        
        const quantity = parseInt(document.getElementById('modalQuantity').value) || 1;
        const basePrice = parseFloat(currentProduct.price.replace(/[^\d.]/g, ''));
        
        // Calculate final price with variants
        let multiplier = 1;
        if (selectedVariants.size === 'large') multiplier += 0.2;
        if (selectedVariants.size === 'extra-large') multiplier += 0.4;
        if (selectedVariants.paper === 'premium') multiplier += 0.3;
        if (selectedVariants.paper === 'luxury') multiplier += 0.5;
        if (selectedVariants.finish === 'glossy') multiplier += 0.1;
        if (selectedVariants.finish === 'satin') multiplier += 0.2;
        
        const finalPrice = basePrice * multiplier;
        
        // Create cart item with variants
        const cartItem = {
            service_id: parseInt(currentProduct.serviceId),
            service_sample_id: parseInt(currentProduct.id),
            quantity: quantity,
            unit_price: finalPrice,
            size: selectedVariants.size,
            paper_type: selectedVariants.paper,
            finish: selectedVariants.finish
        };
        
        // Use cart functionality
        const cart = getLocalCart();
        const existing = cart.find(i => 
            i.service_id == parseInt(currentProduct.serviceId) && 
            i.service_sample_id == parseInt(currentProduct.id) &&
            i.unit_price == finalPrice &&
            i.size == selectedVariants.size &&
            i.paper_type == selectedVariants.paper &&
            i.finish == selectedVariants.finish
        );
        
        if (existing) {
            existing.quantity += quantity;
        } else {
            cart.push(cartItem);
        }
        
        setLocalCart(cart);
        updateNavbarCount();
        
        // Visual feedback
        this.textContent = 'Added to Cart!';
        this.disabled = true;
        this.classList.add('bg-green-600');
        setTimeout(() => {
            this.textContent = 'Add to Cart - ' + document.getElementById('modalTotalPrice').textContent;
            this.disabled = false;
            this.classList.remove('bg-green-600');
        }, 2000);
        
        // Sync to server
        syncToServer(cartItem);
        
        // Close modal after short delay
        setTimeout(() => {
            closeProductGallery();
        }, 1500);
    });
    
    // Quick view button clicks
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('quick-view-btn')) {
            const sampleId = e.target.dataset.sampleId;
            const serviceId = e.target.dataset.serviceId;
            openProductGallery(sampleId, serviceId);
        }
    });
});
</script>

<style>
.variant-btn.active {
    border-color: var(--color-brand-blue);
    color: var(--color-brand-blue);
    background-color: var(--color-brand-blue)/10;
}
</style>
