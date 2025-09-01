{{-- sales.edit --}}
@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8 space-y-12 bg-gray-100 dark:bg-[#0a0a23] dark:text-white">
        <section>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Sale #{{ $sale->id }}</h2>
        </section>

        <section>
            <div class="bg-white dark:bg-[#1a1a3a] p-6 rounded-2xl shadow-md border border-gray-200 dark:border-gray-700 max-w-md mx-auto">
                <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6" id="saleForm">
                    @method('PUT')
                    @csrf

                    @if (session('error'))
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-400 rounded-lg">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label for="customer_name" class="block text-gray-700 dark:text-gray-300">Customer Name <span class="text-red-600">*</span></label>
                        <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', $sale->customer->name ?? '') }}" list="customer_suggestions"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_name') border-red-500 @enderror"
                               required aria-describedby="customer_name-error">
                        <datalist id="customer_suggestions">
                            @foreach($customers as $customer)
                                <option value="{{ $customer->name }}">
                            @endforeach
                        </datalist>
                        @error('customer_name')
                            <p id="customer_name-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="customer_phone" class="block text-gray-700 dark:text-gray-300">Customer Phone</label>
                        <input type="text" name="customer_phone" id="customer_phone" value="{{ old('customer_phone', $sale->customer->phone ?? '') }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('customer_phone') border-red-500 @enderror"
                               aria-describedby="customer_phone-error">
                        @error('customer_phone')
                            <p id="customer_phone-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="saleable_type" class="block text-gray-700 dark:text-gray-300">Product Type <span class="text-red-600">*</span></label>
                        <select name="saleable_type" id="saleable_type" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_type') border-red-500 @enderror"
                                required aria-describedby="saleable_type-error">
                            <option value="" disabled>Select Type</option>
                            <option value="App\Models\Bird" {{ old('saleable_type', $sale->saleable_type) == 'App\Models\Bird' ? 'selected' : '' }}>Bird</option>
                            <option value="App\Models\Egg" {{ old('saleable_type', $sale->saleable_type) == 'App\Models\Egg' ? 'selected' : '' }}>Egg</option>
                        </select>
                        @error('saleable_type')
                            <p id="saleable_type-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="saleable_id" class="block text-gray-700 dark:text-gray-300">Product <span class="text-red-600">*</span></label>
                        <select name="saleable_id" id="saleable_id" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('saleable_id') border-red-500 @enderror"
                                required aria-describedby="saleable_id-error">
                            <option value="" disabled>Select Product</option>
                        </select>
                        @error('saleable_id')
                            <p id="saleable_id-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p id="availabilityInfo" class="text-sm mt-2 text-gray-600 dark:text-gray-300"></p>
                    </div>

                    <div>
                        <label for="product_variant" class="block text-gray-700 dark:text-gray-300">Product Variant <span class="text-red-600">*</span></label>
                        <select name="product_variant" id="product_variant" class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('product_variant') border-red-500 @enderror"
                                required aria-describedby="product_variant-error">
                            <option value="" disabled>Select Variant</option>
                        </select>
                        @error('product_variant')
                            <p id="product_variant-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-gray-700 dark:text-gray-300">Quantity (Crates for Eggs, Number for Birds) <span class="text-red-600">*</span></label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', $sale->quantity) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('quantity') border-red-500 @enderror"
                               min="1" required aria-describedby="quantity-error">
                        @error('quantity')
                            <p id="quantity-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p id="quantityValidation" class="text-sm mt-2 text-red-600 dark:text-red-400 hidden"></p>
                    </div>

                    <div>
                        <label for="unit_price" class="block text-gray-700 dark:text-gray-300">Unit Price <span class="text-red-600">*</span></label>
                        <input type="number" name="unit_price" id="unit_price" value="{{ old('unit_price', $sale->unit_price) }}" step="0.01"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('unit_price') border-red-500 @enderror"
                               min="0" required aria-describedby="unit_price-error">
                        @error('unit_price')
                            <p id="unit_price-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sale_date" class="block text-gray-700 dark:text-gray-300">Sale Date <span class="text-red-600">*</span></label>
                        <input type="date" name="sale_date" id="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}"
                               class="w-full p-2 border rounded dark:bg-gray-800 dark:border-gray-600 dark:text-white @error('sale_date') border-red-500 @enderror"
                               required aria-describedby="sale_date-error">
                        @error('sale_date')
                            <p id="sale_date-error" class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex space-x-4">
                        <button type="submit" id="saveBtn"
                                class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600">
                            Save
                        </button>
                        <a href="{{ route('sales.index') }}"
                           class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </section>
    </div>

    {{-- Confirmation Modal (same markup as create) --}}
    <div id="confirmModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
        <div class="absolute inset-0 bg-black/50" id="confirmBackdrop"></div>
        <div class="bg-white dark:bg-[#0f1724] rounded-2xl shadow-lg p-6 z-10 max-w-lg w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Large stock reduction</h3>
            <p id="confirmText" class="mt-3 text-sm text-gray-700 dark:text-gray-300"></p>
            <div class="mt-6 flex justify-end space-x-3">
                <button id="cancelConfirm" class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-700">Cancel</button>
                <button id="confirmProceed" class="px-4 py-2 rounded bg-red-600 text-white">Proceed & Save</button>
            </div>
        </div>
    </div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const saleableTypeSelect = document.getElementById('saleable_type');
            const saleableIdSelect = document.getElementById('saleable_id');
            const productVariantSelect = document.getElementById('product_variant');
            const quantityInput = document.getElementById('quantity');
            const saveBtn = document.getElementById('saveBtn');
            const quantityValidation = document.getElementById('quantityValidation');
            const availabilityInfo = document.getElementById('availabilityInfo');
            const saleForm = document.getElementById('saleForm');

            const confirmModal = document.getElementById('confirmModal');
            const confirmText = document.getElementById('confirmText');
            const cancelConfirm = document.getElementById('cancelConfirm');
            const confirmProceed = document.getElementById('confirmProceed');

            const birdsData = @json($birdsData ?? []);
            const eggsData = @json($eggsData ?? []);

            const LARGE_DECREASE_THRESHOLD = 0.5;

            function clearSelect(selectEl) {
                while (selectEl.firstChild) selectEl.removeChild(selectEl.firstChild);
            }

            function appendPlaceholder(selectEl, text = 'Select') {
                const placeholder = document.createElement('option');
                placeholder.value = '';
                placeholder.disabled = true;
                placeholder.selected = true;
                placeholder.text = text;
                selectEl.appendChild(placeholder);
            }

            function populateSaleableOptions(type, preselectId = null, preselectVariant = null) {
                clearSelect(saleableIdSelect);
                clearSelect(productVariantSelect);

                appendPlaceholder(saleableIdSelect, 'Select Product');
                appendPlaceholder(productVariantSelect, 'Select Variant');

                if (type === 'App\\Models\\Bird') {
                    if (!birdsData || birdsData.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.disabled = true;
                        opt.text = 'No birds available';
                        saleableIdSelect.appendChild(opt);
                        availabilityInfo.textContent = '';
                        return;
                    }
                    birdsData.forEach(bird => {
                        const option = document.createElement('option');
                        option.value = bird.id;
                        option.text = bird.display;
                        saleableIdSelect.appendChild(option);
                    });
                    ['broiler','layer'].forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v;
                        opt.text = v.charAt(0).toUpperCase() + v.slice(1);
                        productVariantSelect.appendChild(opt);
                    });
                } else if (type === 'App\\Models\\Egg') {
                    if (!eggsData || eggsData.length === 0) {
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.disabled = true;
                        opt.text = 'No egg batches available';
                        saleableIdSelect.appendChild(opt);
                        availabilityInfo.textContent = '';
                        return;
                    }
                    eggsData.forEach(egg => {
                        const option = document.createElement('option');
                        option.value = egg.id;
                        option.text = egg.display;
                        saleableIdSelect.appendChild(option);
                    });
                    ['regular','cracked'].forEach(v => {
                        const opt = document.createElement('option');
                        opt.value = v;
                        opt.text = v.charAt(0).toUpperCase() + v.slice(1);
                        productVariantSelect.appendChild(opt);
                    });
                } else {
                    availabilityInfo.textContent = '';
                }

                // preselect if provided
                if (preselectId) saleableIdSelect.value = preselectId;
                if (preselectVariant) productVariantSelect.value = preselectVariant;
            }

            function findSelectedAvailability() {
                const type = saleableTypeSelect.value;
                const id = saleableIdSelect.value;
                if (!type || !id) return { available: null, meta: {} };

                if (type === 'App\\Models\\Bird') {
                    const bird = birdsData.find(b => String(b.id) === String(id));
                    if (bird) return { available: bird.quantity, meta: bird };
                } else if (type === 'App\\Models\\Egg') {
                    const egg = eggsData.find(e => String(e.id) === String(id));
                    if (egg) return { available: egg.crates, meta: egg };
                }
                return { available: null, meta: {} };
            }

            function showAvailabilityDetails() {
                const { available, meta } = findSelectedAvailability();
                if (available === null) {
                    availabilityInfo.textContent = '';
                    return;
                }
                let parts = [`Available: ${available}`];
                if (meta.stage) parts.push(`Stage: ${meta.stage}`);
                if (meta.egg_size) parts.push(`Size: ${meta.egg_size}`);
                if (meta.is_cracked) parts.push('Cracked: Yes');
                if (meta.pen_name) parts.push(meta.pen_name);
                availabilityInfo.textContent = parts.join(' • ');
            }

            function validateQuantityAgainstStock() {
                const q = parseInt(quantityInput.value, 10);
                const { available } = findSelectedAvailability();

                if (available === null) {
                    quantityValidation.classList.add('hidden');
                    saveBtn.disabled = false;
                    availabilityInfo.textContent = '';
                    return { valid: true, shouldConfirm: false, available };
                }

                availabilityInfo.textContent = `Available: ${available}`;

                if (isNaN(q) || q <= 0) {
                    quantityValidation.classList.add('hidden');
                    saveBtn.disabled = false;
                    return { valid: true, shouldConfirm: false, available };
                }

                if (q > available) {
                    quantityValidation.textContent = `Insufficient stock: only ${available} available.`;
                    quantityValidation.classList.remove('hidden');
                    saveBtn.disabled = true;
                    return { valid: false, shouldConfirm: false, available };
                } else {
                    quantityValidation.classList.add('hidden');
                    const shouldConfirm = (q / available) >= LARGE_DECREASE_THRESHOLD;
                    saveBtn.disabled = false;
                    return { valid: true, shouldConfirm, available, q };
                }
            }

            // initialize with sale values
            const currentType = '{{ old('saleable_type', $sale->saleable_type) }}';
            const currentSaleableId = '{{ old('saleable_id', $sale->saleable_id) }}';
            const currentVariant = '{{ old('product_variant', $sale->product_variant) }}';

            if (currentType) {
                saleableTypeSelect.value = currentType;
                populateSaleableOptions(currentType, currentSaleableId, currentVariant);
                showAvailabilityDetails();
            }

            saleableTypeSelect.addEventListener('change', function() {
                populateSaleableOptions(this.value);
            });

            saleableIdSelect.addEventListener('change', function() {
                showAvailabilityDetails();
                validateQuantityAgainstStock();
            });

            quantityInput.addEventListener('input', function() {
                validateQuantityAgainstStock();
            });

            saleForm.addEventListener('submit', function(e) {
                const { valid, shouldConfirm, available, q } = validateQuantityAgainstStock();
                if (!valid) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
                if (shouldConfirm) {
                    e.preventDefault();
                    e.stopPropagation();
                    confirmText.textContent = `You're about to sell ${q} of ${available} available (${Math.round((q/available)*100)}%). Are you sure? This will significantly reduce stock.`;
                    confirmModal.classList.remove('hidden');
                    return false;
                }
            });

            cancelConfirm.addEventListener('click', function() {
                confirmModal.classList.add('hidden');
            });

            confirmProceed.addEventListener('click', function() {
                confirmModal.classList.add('hidden');
                saleForm.submit();
            });
        });
    </script>
@endpush
@endsection
