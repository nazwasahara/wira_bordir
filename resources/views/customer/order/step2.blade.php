@extends('layouts.order')

@section('title', 'Custom Order')

@section('content')
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage min-h-screen py-16 px-6 md:px-12 lg:px-16">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        <h2 class="text-4xl font-bold font-display text-moss mb-8 mt-2 text-center">Custom Selempang Sesuai Seleramu</h2>

        {{-- NOTE: action harus menuju route saveItems --}}
        <form id="orderForm" action="{{ route('order.saveItems', $order->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div id="itemList">

                {{-- === ITEM TEMPLATE === --}}
                <div class="item border border-gray-300 px-5 py-8 rounded-lg mb-8 bg-white shadow-sm relative">
                    <button type="button" class="remove-item absolute top-2 right-2 bg-red-600 text-white font-extrabold rounded-full px-2 py-1 shadow-sm hidden">X</button>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

                        {{-- Product --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Produk</label>
                            <select name="items[0][product_id]" class="product w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="" class="hover-sage">- Pilih Produk -</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-price="{{ $p->base_price }}">
                                        {{ $p->product_name }} (Rp{{ number_format($p->base_price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bahan --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Bahan</label>
                            <select name="items[0][material_id]" class="material w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="">- Pilih Bahan -</option>
                                @foreach($materials as $m)
                                    <option value="{{ $m->id }}" data-price="{{ $m->price }}">
                                        {{ $m->name }} (Rp{{ number_format($m->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Warna Kain --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Warna Kain</label>
                            <select name="items[0][material_color_id]" class="material_color w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="">- Pilih Warna -</option>
                                @foreach($materialColors as $c)
                                    <option value="{{ $c->id }}" data-material="{{ $c->material_id }}" data-price="{{ $c->price }}">
                                        {{ $c->name }} (Rp{{ number_format($c->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jenis Selempang --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Jenis Selempang</label>
                            <select name="items[0][sash_type_id]" class="sash_type w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="">- Pilih Jenis -</option>
                                @foreach($sashTypes as $st)
                                    <option value="{{ $st->id }}" data-price="{{ $st->price }}">
                                        {{ $st->name }} (Rp{{ number_format($st->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Untuk Selempang Leher (2 sisi) --}}
                        <div class="text-leher hidden">
                            <label class="font-bold text-lg text-text-dark">Teks Sisi Kanan</label>
                            <input type="text" name="items[0][text_right]"
                                class="w-full border focus:border-leaf focus:ring-moss p2 rounded mb-2"
                                placeholder="Tulisan sisi kanan">
                        </div>
                        <div class="text-leher hidden">
                            <label class="font-bold text-lg text-text-dark">Teks Sisi Kiri</label>
                            <input type="text" name="items[0][text_left]"
                                class="w-full border focus:border-leaf focus:ring-moss p2 rounded"
                                placeholder="Tulisan sisi kiri">
                        </div>

                        {{-- Untuk Selempang Samping (1 sisi) --}}
                        <div class="text-samping hidden">
                            <label class="font-bold text-lg text-text-dark">Teks Selempang</label>
                            <input type="text" name="items[0][text_single]"
                                class="text-single w-full border focus:border-leaf focus:ring-moss p2 rounded"
                                placeholder="Tulisan selempang">
                        </div>

                        {{-- Font --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Font</label>
                            <select name="items[0][font_id]" class="fontoption w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="">- Pilih Font -</option>
                                @foreach($fonts as $f)
                                    <option value="{{ $f->id }}" data-price="{{ $f->price }}">
                                        {{ $f->name }} (Rp{{ number_format($f->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Motif Samping --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Motif Samping</label>
                            <select name="items[0][side_motif_id]" class="motif w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                                <option value="">- Pilih Motif -</option>
                                @foreach($sideMotifs as $mo)
                                    <option value="{{ $mo->id }}" data-price="{{ $mo->price }}">
                                        {{ $mo->name }} (Rp{{ number_format($mo->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Warna Pita (tampilkan bila Motif = Pita) --}}
                        <div class="ribbon_wrapper hidden">
                            <label class="font-bold text-lg text-text-dark">Warna Pita</label>
                            <select name="items[0][ribbon_color_id]" class="ribbon_color w-full border focus:border-leaf focus:ring-moss p2 rounded">
                                <option value="">- Pilih Warna Pita -</option>
                                @foreach($ribbonColors as $rc)
                                    <option value="{{ $rc->id }}" data-price="{{ $rc->price }}">{{ $rc->name }} (Rp{{ number_format($rc->price) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Renda (tampilkan bila Motif = Renda) --}}
                        <div class="lace_wrapper hidden">
                            <label class="font-bold text-lg text-text-dark">Pilihan Renda</label>
                            <select name="items[0][lace_option_id]" class="lace_option w-full border focus:border-leaf focus:ring-moss p2 rounded">
                                <option value="">- Pilih Renda (warna & ukuran) -</option>
                                @foreach($laceOptions as $lo)
                                    <option value="{{ $lo->id }}" data-price="{{ $lo->price }}">{{ $lo->color }} - {{ $lo->size }} (Rp{{ number_format($lo->price) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rombe (tampilkan bila Motif = Rombe) --}}
                        <div class="rombe_wrapper hidden">
                            <label class="font-bold text-lg text-text-dark">Pilihan Rombe</label>
                            <select name="items[0][rombe_option_id]" class="rombe_option w-full border focus:border-leaf focus:ring-moss p2 rounded">
                                <option value="">- Pilih Rombe (warna & ukuran) -</option>
                                @foreach($rombeOptions as $ro)
                                    <option value="{{ $ro->id }}" data-price="{{ $ro->price }}">{{ $ro->color }} - {{ $ro->size }} (Rp{{ number_format($ro->price) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pita Motif (tampilkan bila Motif = Pita Motif --}}
                        <div class="motifribbon_wrapper hidden">
                            <label class="font-bold text-lg text-text-dark">Pilihan Pita Motif</label>
                            <select name="items[0][motif_ribbon_option_id]" class="motif_ribbon_option w-full border focus:border-leaf focus:ring-moss p2 rounded">
                                <option value="">- Pilih Pita Motif (warna & ukuran) -</option>
                                @foreach($motifRibbonOptions as $mro)
                                    <option value="{{ $mro->id }}" data-price="{{ $mro->price }}">{{ $mro->color }} - {{ $mro->size }} (Rp{{ number_format($mro->price) }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Item Tambahan --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Item Tambahan</label>
                            <select name="items[0][additional_item_option_id]" class="additional_option w-full border focus:border-leaf focus:ring-moss p2 rounded">
                                <option value="">- Pilih Permata / Logo -</option>
                                @foreach($additionalItems as $aio)
                                    <option value="{{ $aio->id }}" data-price="{{ $aio->price }}">
                                        {{ $aio->item_name ?? $aio->model.' - '.$aio->color }} (Rp{{ number_format($aio->price) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Quantity --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark">Jumlah Selempang</label>
                            <input type="number" name="items[0][quantity]" value="1" min="1"
                                class="quantity w-full border focus:border-leaf focus:ring-moss p2 rounded" required>
                        </div>

                        {{-- Logo --}}
                        <div>
                            <label class="font-bold text-lg text-text-dark"> Upload Logo <small class="text-gray-500 font-normal">(jika memilih logo)</small></label>
                            <input type="file"
                                name="items[0][logo]"
                                accept="image/*"
                                class="logo w-full border rounded p-2 bg-white text-sm">

                            {{-- PREVIEW --}}
                            <img class="logo-preview mt-2 h-30 object-contain hidden">
                            <small class="text-gray-500">Format: JPG, JPEG, PNG, SVG. Max 2MB</small>
                        </div>

                        {{-- Hidden final_price (supaya dikirim ke controller) --}}
                        <input type="hidden" name="items[0][final_price]" class="final_price_input" value="0">

                    </div>

                    {{-- Total Harga Per Item --}}
                    <p class="mt-6 font-bold text-xl text-text-dark">Harga Selempang: Rp<span class="itemTotal">0</span></p>

                </div>
            </div>

            {{-- Add Item --}}
            <div class="flex items-center gap-4">
                <button type="button" id="addItem" class="bg-leaf hover:bg-kuning hover:text-text-dark transition-colors text-md font-semibold text-white px-4 py-2 rounded-lg uppercase tracking-wide">
                    Tambah Selempang
                </button>

                <small class="text-sm text-gray-500">Tambah lebih dari satu selempang jika perlu.</small>
            </div>

            {{-- Grand Total --}}
            <p class="text-xl font-bold mt-6 text-right">Total Semua: Rp<span id="grandTotal">0</span></p>

            {{-- Submit --}}
            <div class="flex justify-center mt-6">
                <button type="submit" class="flex items-center gap-1 bg-leaf hover:bg-rose text-lg font-bold transition-colors text-white pl-7 pr-6 py-3 rounded-full uppercase tracking-wide">
                    Konfirmasi dan Bayar
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</section>

@push('scripts')
<script>
    $(function(){
        let index = 1;

        // Helper: update name indexes for cloned item
        function reindexClone(clone, idx) {
            clone.find("input, select").each(function(){
                let name = $(this).attr("name");
                if(!name) return;
                name = name.replace(/\[\d+\]/, '['+idx+']');
                $(this).attr("name", name);
            });
            clone.find(".itemTotal").text('0');
            clone.find(".final_price_input").val(0);
        }

        function formatIDR(number) {
            return number.toLocaleString('id-ID');
        }

        // Hitung semua harga per item selempang
        function calculateItemTotal(itemBox) {
            const productOption = itemBox.find(".product option:selected");

            if (!productOption.val()) {
                // Reset semua field selain produk
                itemBox.find("select").each(function() {
                    if (!$(this).hasClass('product')) { // kecuali produk
                        $(this).val('');
                        if($(this).hasClass('material_color')) {
                            $(this).find('option').show(); // semua opsi warna tampil
                        }
                    }
                });
                itemBox.find("input[type='number']").val(1);
                itemBox.find("input[type='text']").val('');
                itemBox.find("input[type='file']").val(null);
                itemBox.find(".logo-preview").attr("src", "").addClass("hidden");
                itemBox.find(".ribbon_wrapper, .lace_wrapper, .rombe_wrapper, .motifribbon_wrapper").addClass('hidden');
                itemBox.find(".text-leher, .text-samping").addClass('hidden');

                // harga 0
                itemBox.find(".itemTotal").text('0');
                itemBox.find(".final_price_input").val(0);

                updateGrandTotal();
                return;
            }

            // Produk sudah dipilih → hitung harga normal
            let total = Number(productOption.data("price") || 0);

            // Material
            const materialOption = itemBox.find(".material option:selected");
            if (materialOption.val()) total += Number(materialOption.data("price") || 0);

            // Material Color
            const colorOption = itemBox.find(".material_color option:selected");
            if (colorOption.val()) total += Number(colorOption.data("price") || 0);

            // Sash Type, Font, Motif, Ribbon, Lace, Rombe, Motif Ribbon, Additional
            itemBox.find(".sash_type option:selected, .fontoption option:selected, .motif option:selected, .ribbon_color option:selected, .lace_option option:selected, .rombe_option:selected, .motif_ribbon_option:selected, .additional_option option:selected")
                .each(function(){
                    total += Number($(this).data("price") || 0);
                });

            const qty = Number(itemBox.find(".quantity").val() || 1);
            const perItem = Math.round(total);
            const subtotal = perItem * qty;

            itemBox.find(".itemTotal").text(formatIDR(subtotal));
            itemBox.find(".final_price_input").val(perItem);

            updateGrandTotal();
        }

        function updateGrandTotal() {
            let total = 0;
            $(".item").each(function(){
                const subtotalText = $(this).find(".itemTotal").text().replace(/\./g,'') || '0';
                total += parseInt(subtotalText) || 0;
            });
            $("#grandTotal").text(formatIDR(total));
        }

        // initial calculate for first item
        calculateItemTotal($(".item:first"));

        // Toggle motif options
        function toggleMotifOptions(itemBox) {
            const motifOption = itemBox.find(".motif option:selected");
            const motif = motifOption.val() ? motifOption.text().toLowerCase() : '';

            // Jika motif belum dipilih
            if (!motif) {
                itemBox.find('.ribbon_wrapper, .lace_wrapper, .rombe_wrapper, .motifribbon_wrapper').addClass('hidden')
                    .find('select').val('');
                return;
            }

            // Ribbon
            if (motif.includes('pita') && !motif.includes('motif')) {
                itemBox.find('.ribbon_wrapper').removeClass('hidden');
            } else {
                itemBox.find('.ribbon_wrapper').addClass('hidden').find('select').val('');
            }

            // Renda
            if (motif.includes('renda')) {
                itemBox.find('.lace_wrapper').removeClass('hidden');
            } else {
                itemBox.find('.lace_wrapper').addClass('hidden').find('select').val('');
            }

            // Rombe
            if (motif.includes('rombe')) {
                itemBox.find('.rombe_wrapper').removeClass('hidden');
            } else {
                itemBox.find('.rombe_wrapper').addClass('hidden').find('select').val('');
            }

            // Pita Motif
            if (motif.includes('pita motif') || motif.includes('motif')) {
                itemBox.find('.motifribbon_wrapper').removeClass('hidden');
            } else {
                itemBox.find('.motifribbon_wrapper').addClass('hidden').find('select').val('');
            }
        }

        // On change (product, material, motif, etc)
        $(document).on("change keyup", ".item select, .item .quantity", function() {
            const itemBox = $(this).closest(".item");
            toggleMotifOptions(itemBox);
            calculateItemTotal(itemBox);
        });

        // Filter warna sesuai bahan
        $(document).on("change", ".material", function () {
            const itemBox = $(this).closest(".item");
            const materialId = $(this).val();
            const colorSelect = itemBox.find(".material_color");

            colorSelect.find("option").each(function () {
                const allowedMaterial = $(this).data("material");
                if (!allowedMaterial || allowedMaterial == materialId) $(this).show();
                else $(this).hide().prop("selected", false);
            });

            // langsung update harga bahan
            calculateItemTotal(itemBox);
        });

        $(document).on("change", ".material_color", function() {
            const itemBox = $(this).closest(".item");
            calculateItemTotal(itemBox);
        });

        // Show/hide input teks sesuai sash type
        $(document).on("change", ".sash_type", function () {
            const itemBox = $(this).closest(".item");
            const type = $(this).val();
            if (type == 1) { // Leher
                itemBox.find(".text-leher").removeClass("hidden");
                itemBox.find(".text-samping").addClass("hidden");
                itemBox.find(".text-single").val("");
            } else { // Samping
                itemBox.find(".text-leher").addClass("hidden");
                itemBox.find(".text-samping").removeClass("hidden");
                itemBox.find(".text-right, .text-left").val("");
            }
        });

        // Preview Logo
        $(document).on("change", ".logo", function () {
            const input = this;
            const preview = $(this).closest(".item").find(".logo-preview");

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.attr("src", e.target.result).removeClass("hidden");
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.addClass("hidden").attr("src", "");
            }
        });

        // Add Item Selempang (clone) //
        $("#addItem").on("click", function() {
            let clone = $(".item:first").clone();

            clone.find("select").val('');
            
            clone.find("input").each(function(){
                if($(this).attr('type') === 'number') $(this).val(1);
                else $(this).val('');
            });

            clone.find("input[type='file']").val(null);
            clone.find(".logo-preview").attr("src", "").addClass("hidden");
            clone.find('.ribbon_wrapper, .lace_wrapper, .rombe_wrapper, .motifribbon_wrapper').addClass('hidden');

            clone.find(".remove-item").removeClass("hidden");

            clone.appendTo("#itemList");

            reindexClone(clone, index);
            calculateItemTotal(clone);

            index++;
        });

        // Remove Item Selempang //
        $(document).on("click", ".remove-item", function() {
            $(this).closest(".item").remove();
            updateGrandTotal();
        });

        // Before submit
        $("#orderForm").on("submit", function(e){
            $(".item").each(function(){
                calculateItemTotal($(this));
            });
            if ($(".item").length === 0) {
                alert('Tambah minimal 1 selempang.');
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush

@endsection