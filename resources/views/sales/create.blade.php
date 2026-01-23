{{-- sales.create --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-8">
    <section class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-2">Add New Sale</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Record a sale quickly — availability, totals and validation are shown live.</p>
    </section>

    <section class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700">
            {{-- Server side flash/errors (unchanged) --}}
            @if (session('error'))
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center mb-4">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg mb-4">
                    <strong class="block mb-1">Please fix the following:</strong>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" id="saleForm" novalidate>
                @csrf

                <!-- Customer -->
                <div>
                    <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                    <div class="flex gap-2">
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name') }}" list="customer_suggestions"
                               class="flex-1 p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_name') border-red-500 @enderror"
                               required aria-describedby="customer_name-error" placeholder="Start typing a customer name...">
                        <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}"
                               class="w-44 p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_phone') border-red-500 @enderror"
                               placeholder="Phone (optional)">
                    </div>
                    <datalist id="customer_suggestions">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->name }}">
                        @endforeach
                    </datalist>
                    @error('customer_name')
                        <p id="customer_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Product Type -->
                    <div>
                        <label for="saleable_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Type</label>
                        <select name="saleable_type" id="saleable_type"
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_type') border-red-500 @enderror"
                                required aria-describedby="saleable_type-error">
                            <option value="" disabled {{ old('saleable_type') ? '' : 'selected' }}>Select Type</option>
                            <option value="App\Models\Bird" {{ old('saleable_type') == 'App\Models\Bird' ? 'selected' : '' }}>Bird</option>
                            <option value="App\Models\Egg" {{ old('saleable_type') == 'App\Models\Egg' ? 'selected' : '' }}>Egg</option>
                        </select>
                        @error('saleable_type')
                            <p id="saleable_type-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product -->
                    <div>
                        <label for="saleable_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product</label>
                        <select name="saleable_id" id="saleable_id"
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_id') border-red-500 @enderror"
                                required aria-describedby="saleable_id-error">
                            <option value="" disabled {{ old('saleable_id') ? '' : 'selected' }}>Select Product</option>
                        </select>
                        @error('saleable_id')
                            <p id="saleable_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Variant & Quantity Row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="product_variant" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Variant</label>
                        <select name="product_variant" id="product_variant"
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('product_variant') border-red-500 @enderror"
                                required aria-describedby="product_variant-error">
                            <option value="" disabled {{ old('product_variant') ? '' : 'selected' }}>Select Variant</option>
                        </select>
                        @error('product_variant')
                            <p id="product_variant-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantity <span id="unitHint" class="text-xs text-gray-500 dark:text-gray-400 ml-1">Units</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror"
                               min="1" required aria-describedby="quantity-error" placeholder="0">
                        <p id="quantity-error" class="text-sm mt-1 text-red-600 dark:text-red-400 hidden"></p>
                    </div>

                    <div>
                        <label for="unit_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Unit Price (₵)</label>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price') }}" step="0.01"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('unit_price') border-red-500 @enderror"
                               min="0" required aria-describedby="unit_price-error" placeholder="0.00">
                        @error('unit_price')
                            <p id="unit_price-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="sale_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sale Date</label>
                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', now()->format('Y-m-d')) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sale_date') border-red-500 @enderror"
                               required aria-describedby="sale_date-error">
                        @error('sale_date')
                            <p id="sale_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="due_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date (optional)</label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('due_date') border-red-500 @enderror"
                               aria-describedby="due_date-error">
                        @error('due_date')
                            <p id="due_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Live info row -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                    <div class="p-3 rounded border dark:border-gray-700 bg-gray-50 dark:bg-[#071028]">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Availability</p>
                        <p id="availabilityInfo" class="text-sm font-medium text-gray-800 dark:text-gray-100 mt-1">—</p>
                    </div>

                    <div class="p-3 rounded border dark:border-gray-700 bg-gray-50 dark:bg-[#071028]">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Estimated Total</p>
                        <p id="estimatedTotal" class="text-lg font-bold text-green-600 dark:text-green-400 mt-1">₵ 0.00</p>
                        <p id="calcHint" class="text-xs text-gray-500 dark:text-gray-400 mt-1 hidden">Calculated as Quantity × Unit Price</p>
                    </div>

                    <div class="p-3 rounded border dark:border-gray-700 bg-gray-50 dark:bg-[#071028]">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Validation</p>
                        <p id="validationSummary" class="text-sm text-gray-700 dark:text-gray-200 mt-1">No issues</p>
                    </div>
                </div>

                <!-- One-step Payment Option -->
                <div class="p-4 rounded border dark:border-gray-700 bg-gray-50 dark:bg-[#071028]">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" id="pay_now" name="pay_now" class="form-checkbox h-4 w-4 text-blue-600">
                        <span class="text-sm text-gray-700 dark:text-gray-200 font-medium">Take payment now (one-step)</span>
                    </label>

                    <div id="payNowFields" class="mt-3 grid grid-cols-1 md:grid-cols-4 gap-3 items-end hidden">
                        <div>
                            <label for="payment_amount" class="block text-xs text-gray-500 dark:text-gray-400">Amount (₵)</label>
                            <input type="number" id="payment_amount" name="payment_amount" step="0.01" min="0.01" 
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white" 
                                placeholder="0.00">
                        </div>

                        <div>
                            <label for="payment_date" class="block text-xs text-gray-500 dark:text-gray-400">Date</label>
                            <input type="date" id="payment_date" name="payment_date" 
                                value="{{ now()->format('Y-m-d') }}"
                                class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                        </div>

                        <div>
                            <label for="payment_method" class="block text-xs text-gray-500 dark:text-gray-400">Method</label>
                            <select id="payment_method" name="payment_method" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                <option value="cash" selected>Cash</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="bank_transfer">Bank Transfer</option>
                            </select>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Balance due</p>
                            <p id="paymentBalance" class="text-sm font-medium text-gray-800 dark:text-gray-100 mt-1">₵ 0.00</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-700 dark:text-white hover:bg-gray-300 transition">
                        Cancel
                    </a>
                    <button type="submit" id="saveBtn" class="inline-flex items-center px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition" disabled>
                        Save Sale
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Confirmation Modal --}}
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-black/50" id="confirmBackdrop" tabindex="-1" aria-hidden="true"></div>
        <div class="bg-white dark:bg-[#0f1724] rounded-2xl shadow-lg p-6 z-10 max-w-lg w-full mx-4" role="dialog" aria-modal="true" aria-labelledby="confirmTitle">
            <h3 id="confirmTitle" class="text-lg font-semibold text-gray-800 dark:text-white">Confirm large sale</h3>
            <p id="confirmText" class="mt-3 text-sm text-gray-700 dark:text-gray-300"></p>
            <div class="mt-6 flex justify-end space-x-3">
                <button id="cancelConfirm" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700">Cancel</button>
                <button id="confirmProceed" class="px-4 py-2 rounded bg-red-600 text-white">Proceed & Save</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const saleForm = document.getElementById('saleForm');
    const saleableType = document.getElementById('saleable_type');
    const saleableId = document.getElementById('saleable_id');
    const productVariant = document.getElementById('product_variant');
    const quantity = document.getElementById('quantity');
    const unitPrice = document.getElementById('unit_price');
    const availabilityInfo = document.getElementById('availabilityInfo');
    const estimatedTotal = document.getElementById('estimatedTotal');
    const calcHint = document.getElementById('calcHint');
    const validationSummary = document.getElementById('validationSummary');
    const quantityError = document.getElementById('quantity-error');
    const unitHint = document.getElementById('unitHint');
    const saveBtn = document.getElementById('saveBtn');

    // confirmation modal
    const confirmModal = document.getElementById('confirmModal');
    const confirmText = document.getElementById('confirmText');
    const cancelConfirm = document.getElementById('cancelConfirm');
    const confirmProceed = document.getElementById('confirmProceed');

    // Data from server
    const birdsData = @json($birdsData ?? []);
    const eggsData = @json($eggsData ?? []);

    // threshold for asking confirmation (fraction)
    const LARGE_DECREASE_THRESHOLD = 0.5;

    // Helpers
    function clearSelect(select) {
        select.innerHTML = '';
    }
    function addPlaceholder(select, text = 'Select') {
        const opt = document.createElement('option');
        opt.value = '';
        opt.disabled = true;
        opt.selected = true;
        opt.text = text;
        select.appendChild(opt);
    }
    function formatCurrency(num) {
        return '₵ ' + (Number(num) || 0).toFixed(2);
    }
    
    function getTotalVariantStock(type, variant) {
        // We usually only apply spill-over to Eggs
        if (type !== 'App\\Models\\Egg' || !variant) return null;
        
        return eggsData
            .filter(e => {
                const eggVariant = e.is_cracked ? 'cracked' : 'regular';
                return eggVariant === variant;
            })
            .reduce((sum, e) => sum + Number(e.crates), 0);
    }
    // Populate product lists depending on type
    function populateProductLists() {
        clearSelect(saleableId);
        clearSelect(productVariant);
        addPlaceholder(saleableId, 'Select Product');
        addPlaceholder(productVariant, 'Select Variant');

        const t = saleableType.value;
        if (t === 'App\\Models\\Bird') {
            // birds data
            if (!birdsData.length) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.text = 'No birds available';
                saleableId.appendChild(opt);
            } else {
                birdsData.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.id;
                    opt.text = b.display || `${b.breed || 'Bird'} (${b.type || ''})`;
                    saleableId.appendChild(opt);
                });
            }
            // variants: stage/type simplified
            ['adult','chick','broiler','layer'].forEach(v => {
                const o = document.createElement('option');
                o.value = v;
                o.text = v.charAt(0).toUpperCase() + v.slice(1);
                productVariant.appendChild(o);
            });
            unitHint.textContent = 'Units';
        } else if (t === 'App\\Models\\Egg') {
            if (!eggsData.length) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.text = 'No egg batches available';
                saleableId.appendChild(opt);
            } else {
                eggsData.forEach(e => {
                    const opt = document.createElement('option');
                    opt.value = e.id;
                    opt.text = e.display || `Laid ${e.date_laid || ''} • ${e.crates || 0} crates`;
                    saleableId.appendChild(opt);
                });
            }
            ['regular','cracked'].forEach(v => {
                const o = document.createElement('option');
                o.value = v;
                o.text = v.charAt(0).toUpperCase() + v.slice(1);
                productVariant.appendChild(o);
            });
            unitHint.textContent = 'Crates';
        } else {
            unitHint.textContent = 'Units';
        }

        // restore old values if present (Blade old() will set value attributes)
        @if(old('saleable_id'))
            saleableId.value = '{{ old('saleable_id') }}';
        @endif
        @if(old('product_variant'))
            productVariant.value = '{{ old('product_variant') }}';
        @endif
    }

    function findAvailability() {
        const t = saleableType.value;
        const id = saleableId.value;
        if (!t || !id) return { available: null, meta: null };

        if (t === 'App\\Models\\Bird') {
            const b = birdsData.find(x => String(x.id) === String(id));
            if (!b) return { available: null, meta: null };
            return { available: Number(b.quantity || 0), meta: b };
        } else if (t === 'App\\Models\\Egg') {
            const e = eggsData.find(x => String(x.id) === String(id));
            if (!e) return { available: Number(e.crates || 0), meta: e };
            return { available: Number(e.crates || 0), meta: e };
        }
        return { available: null, meta: null };
    }

    function updateAvailabilityUI() {
        const { available } = findAvailability();
        const type = saleableType.value;
        const variant = productVariant.value;

        if (available === null) {
            availabilityInfo.innerHTML = '<span class="text-gray-400 italic">Select product...</span>';
            return;
        }

        let html = `<span class="text-gray-600">This Batch:</span> <strong>${available}</strong>`;
        
        if (type === 'App\\Models\\Egg' && variant) {
            const total = getTotalVariantStock(type, variant);
            html = `
                <div class="flex flex-col">
                    <span class="text-blue-600 font-bold">Total ${variant} Available: ${total}</span>
                    <span class="text-xs text-gray-500">From this batch: ${available}</span>
                </div>
            `;
        }
        
        availabilityInfo.innerHTML = html;
    }

    function setVariantFromMeta(meta, t) {
        let variantVal = '';
        if (t === 'App\\Models\\Bird') {
            variantVal = meta.stage || meta.type || '';
        } else if (t === 'App\\Models\\Egg') {
            variantVal = meta.is_cracked ? 'cracked' : 'regular';
        }
        if (variantVal && [...productVariant.options].some(o => o.value === variantVal)) {
            productVariant.value = variantVal;
        } else if (variantVal) {
            // If variant not in options, add it
            const opt = document.createElement('option');
            opt.value = variantVal;
            opt.text = variantVal.charAt(0).toUpperCase() + variantVal.slice(1);
            productVariant.appendChild(opt);
            productVariant.value = variantVal;
        }
    }

    function updateEstimatedTotal() {
        const q = Number(quantity.value) || 0;
        const p = Number(unitPrice.value) || 0;
        const total = q * p;
        estimatedTotal.textContent = formatCurrency(total);
        calcHint.classList.toggle('hidden', !(q > 0 && p > 0));
    }

    function validateForm() {
    let ok = true;
    let messages = [];

    const type = saleableType.value;
    const variant = productVariant.value;
    const q = Number(quantity.value) || 0;

    // FIND STOCK: If it's eggs, check the whole variant. Otherwise, check the batch.
    let totalAvailable = 0;
    if (type === 'App\\Models\\Egg' && variant) {
        totalAvailable = getTotalVariantStock(type, variant);
    } else {
        const { available } = findAvailability();
        totalAvailable = available;
    }

    // Required checks
    if (!saleableType.value) { ok = false; messages.push('Product type is required'); }
    if (!saleableId.value) { ok = false; messages.push('Product selection is required'); }
    if (!productVariant.value) { ok = false; messages.push('Product variant is required'); }
    if (!q || q <= 0) { ok = false; messages.push('Enter a quantity'); }

    // STOCK CHECK
    if (totalAvailable !== null && q > totalAvailable) {
        ok = false;
        const unit = unitHint.textContent;
        messages.push(`Insufficient total stock: only ${totalAvailable} ${unit} available.`);
        quantityError.textContent = `Insufficient stock across all batches (${totalAvailable} total).`;
        quantityError.classList.remove('hidden');
    } else {
        quantityError.classList.add('hidden');
    }

    // Update UI
    if (!ok) {
        validationSummary.textContent = messages.join(' • ');
        validationSummary.className = 'text-sm text-red-600 dark:text-red-400';
    } else {
        validationSummary.textContent = 'Looks good';
        validationSummary.className = 'text-sm text-green-600 dark:text-green-400';
    }

    const p = Number(unitPrice.value) || 0;
    saveBtn.disabled = !(ok && p >= 0);

    return { 
        ok, 
        shouldConfirm: (totalAvailable !== null && q > 0 && (q / totalAvailable) >= LARGE_DECREASE_THRESHOLD), 
        q, 
        available: totalAvailable 
    };
    }
    // Event wiring
    saleableType.addEventListener('change', function() {
        populateProductLists();
        updateAvailabilityUI();
        validateForm();
    });

    saleableId.addEventListener('change', function() {
        updateAvailabilityUI();
        const t = saleableType.value;
        const { meta } = findAvailability();
        if (meta) {
            setVariantFromMeta(meta, t);
        }
        validateForm();
    });

    productVariant.addEventListener('change', function() {
    updateAvailabilityUI();
        validateForm();
    });

    quantity.addEventListener('input', function() {
        updateEstimatedTotal();
        validateForm();
    });

    unitPrice.addEventListener('input', function() {
        updateEstimatedTotal();
        validateForm();
    });

    // On submit: if large reduction then show confirm modal
    saleForm.addEventListener('submit', function (e) {
        const v = validateForm();
        if (!v.ok) {
            e.preventDefault();
            e.stopPropagation();
            return false;
        }
        if (v.shouldConfirm) {
            e.preventDefault();
            e.stopPropagation();
            const percent = Math.round((v.q / v.available) * 100);
            confirmText.textContent = `You're about to sell ${v.q} of ${v.available} available (${percent}%). This will significantly reduce stock. Do you want to proceed?`;
            confirmModal.classList.remove('hidden');
            cancelConfirm.focus();
            return false;
        }
        // else allow regular submit
    });

    cancelConfirm.addEventListener('click', function() {
        confirmModal.classList.add('hidden');
    });

    confirmProceed.addEventListener('click', function() {
        confirmModal.classList.add('hidden');
        saleForm.submit();
    });

    // Initialize UI with old inputs if present
    populateProductLists();
    updateAvailabilityUI();
    updateEstimatedTotal();
    validateForm();

    // Accessibility: close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !confirmModal.classList.contains('hidden')) {
            confirmModal.classList.add('hidden');
        }
    });

    // --- NEW: Pay Now Toggle Logic ---
    const payNowCheckbox = document.getElementById('pay_now');
    const payNowFields = document.getElementById('payNowFields');
    const paymentAmountInput = document.getElementById('payment_amount');
    const paymentBalanceDisplay = document.getElementById('paymentBalance');

    payNowCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Show fields
            payNowFields.classList.remove('hidden');
            
            // Auto-fill amount from the estimated total
            // Remove non-numeric characters (like '₵ ') to get the raw number
            const currentTotal = parseFloat(estimatedTotal.textContent.replace(/[^\d.-]/g, ''));
            
            if (!isNaN(currentTotal) && currentTotal > 0) {
                paymentAmountInput.value = currentTotal.toFixed(2);
                paymentBalanceDisplay.textContent = '₵ 0.00'; // Fully paid
            }
        } else {
            // Hide fields and clear value so controller ignores it
            payNowFields.classList.add('hidden');
            paymentAmountInput.value = ''; 
        }
    });

    // Update Balance Display when typing custom payment amount
        paymentAmountInput.addEventListener('input', function() {
        const total = parseFloat(estimatedTotal.textContent.replace(/[^\d.-]/g, '')) || 0;
        const paid = parseFloat(this.value) || 0;
        const balance = total - paid;
        
        if (balance < 0) {
            paymentBalanceDisplay.innerHTML = `<span class="text-blue-500">Overpayment: ₵${Math.abs(balance).toFixed(2)}</span>`;
        } else {
            paymentBalanceDisplay.textContent = formatCurrency(balance);
        }
    });

    // Add this to the end of your DOMContentLoaded block
    if (payNowCheckbox.checked || "{{ old('pay_now') }}" === "on") {
        payNowCheckbox.checked = true;
        payNowFields.classList.remove('hidden');
    }
});
</script>
@endpush
@endsection