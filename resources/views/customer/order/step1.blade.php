@extends('layouts.order')

@section('title', 'Data Pemesan')

@section('content')
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage min-h-screen py-20 px-6 md:px-12 lg:px-16">
    <div class="max-w-3xl mx-auto bg-white p-6 rounded-2xl shadow-lg">
        <h2 class="text-4xl font-bold font-display text-moss mb-6 text-center">Data Diri Pemesan</h2>

        <form action="{{ route('order.saveStep1') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block font-bold text-text-dark mb-1">Nama Lengkap</label>
                <input type="text" name="customer_name" 
                    value="{{ old('customer_name', $user->username) }}" 
                    class="w-full border focus:border-leaf focus:ring-moss p-2 rounded" placeholder="John Doe">
            </div>

            <div class="mb-4">
                <label class="block font-bold text-text-dark mb-1">No Telepon</label>
                <input type="number" name="customer_phone_number" 
                    value="{{ old('customer_phone_number', $user->phone_number ?? '') }}" 
                    class="w-full border focus:border-leaf focus:ring-moss p-2 rounded" placeholder="081234567890">
            </div>

            <div class="mb-4">
                <label class="block font-bold text-text-dark mb-1">Alamat Lengkap</label>
                <textarea name="customer_address" 
                    class="w-full border focus:border-leaf focus:ring-moss p-2 rounded" placeholder="Jl. Raya, No. 123">{{ old('customer_address', $user->address ?? '') }}</textarea>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" 
                    class="flex items-center gap-1 pl-7 pr-6 py-3 bg-leaf text-white font-bold rounded-full hover:bg-rose transition-colors shadow-md uppercase tracking-wider">
                    Next
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
    $(document).ready(function() {

        // Fungsi validasi form
        function validateForm(form) {
            let valid = true;
            $(form).find("input, textarea").each(function(){
                // Hapus error sebelumnya
                $(this).next(".error-text").remove();

                if($(this).val().trim() === "") {
                    valid = false;
                    $(this).after('<p class="error-text text-red-600 text-sm mt-1">Field ini wajib diisi.</p>');
                }
            });
            return valid;
        }

        // Submit form
        $("form").on("submit", function(e){
            if(!validateForm(this)){
                e.preventDefault();
                $("html, body").animate({ scrollTop: $(".error-text:first").offset().top - 100 }, 300);
            }
        });

        // Hapus error saat user mengetik lagi
        $("form input, form textarea").on("input", function(){
            $(this).next(".error-text").remove();
        });

    });
</script>
@endpush

@endsection
