@extends('layouts.customer')

@section('title', 'Home')

@section('content')

<!-- Hero Section -->
<section class="bg-linear-to-br from-white/75 to-sage  min-h-screen pt-24 flex items-center" id="hero">
    <div class="max-w-7xl mx-auto px-6 md:px-12 lg:px-16 py-16 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-7 gap-12 items-center">

            <!-- Text -->
            <div class="lg:col-span-4 bg-white/90 p-8 md:p-10 rounded-xl shadow-lg text-center lg:text-left space-y-8">
                <div class="space-y-6">

                    <h1 class="font-display font-bold text-4xl md:text-5xl lg:text-5xl xl:text-6xl leading-tight text-moss">
                        Selempang Premium untuk Momen Terbaik Anda
                    </h1>

                    <p class="text-md md:text-md lg:text-lg xl:text-lg text-text-dark/90  mx-auto lg:mx-0">
                        Custom untuk semua jenis acara — dikerjakan cepat dengan bahan pilihan terbaik.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('gallery') }}" class="px-8 py-3 bg-leaf text-white font-medium rounded-full hover:bg-rose transition-colors shadow-md">
                        Gallery
                    </a>
                    <a href="{{ route('order.step1') }}" class="px-8 py-3 border border-text-dark text-text-dark font-medium rounded-full hover:bg-rose hover:text-white hover:border-white transition-colors">
                        Order Now
                    </a>
                </div>
            </div>

            <!-- Video -->
            <div class="lg:col-span-3 rounded-2xl overflow-hidden shadow-xl animate-float">
                <video autoplay loop muted playsinline class="w-full h-full object-cover">
                    <source src="{{ asset('videos/hero.mp4') }}" type="video/mp4">
                </video>
            </div>

        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="bg-white/90 py-16 px-6 md:px-12 lg:px-16" id="about">
    <div class="max-w-7xl mx-auto">

        <!-- Section Header -->
        <div class="text-center mb-16 px-4">
            <h2 class="font-display font-bold text-4xl md:text-4xl lg:text-5xl xl:text-5xl text-center text-moss mb-12">About Us</h2>
        </div>

        <!-- Main Content -->
        <div class="bg-linear-to-r from-white/75 via-white/90 to-sage rounded-xl shadow-lg p-12 mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                <!-- Text Content -->
                <div>
                    <div class="space-y-4 text-text-dark leading-relaxed">
                        <p class="text-md md:text-md lg:text-lg xl:text-lg">
                            Wira Bordir Computer adalah penyedia layanan bordir selempang premium yang berkomitmen menghadirkan hasil rapi, elegan, dan berkesan untuk setiap acara. 
                            Kami melayani pemesanan selempang custom untuk wisuda, penghargaan, organisasi, perlombaan, hingga berbagai acara formal maupun non-formal lainnya.
                        </p>
                        <p class="text-md md:text-md lg:text-lg xl:text-lg">
                            Dengan dukungan tim berpengalaman dan proses produksi modern, setiap selempang dikerjakan secara teliti mulai dari pemilihan bahan hingga proses bordir untuk memastikan hasil akhir berkualitas tinggi dan tahan lama.
                        </p>
                    </div>
                </div>

                <!-- Company Image -->
                <div class="rounded-xl overflow-hidden shadow-lg w-full h-full object-cover">
                    <img src="{{ asset('images/mesin.jpeg') }}" alt="Wira Bordir">
                </div>

            </div>
        </div>

    </div>
</section>

<!-- Services Section -->
<section class="bg-linear-to-b from-white/90 to-sage py-20 px-6 md:px-12 lg:px-16" id="service">
    <div class="max-w-7xl mx-auto">
        <h2 class="font-display font-bold text-4xl md:text-4xl lg:text-5xl xl:text-5xl text-center text-moss mb-12">Why Choose Us</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <!-- Quality -->
            <div class="bg-white/90 p-8 rounded-xl shadow-md text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-leaf/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <!-- Benang Jahit Icon -->
                    <svg fill="#344E41" width="38" height="38" viewBox="0 0 297 297" xmlns="http://www.w3.org/2000/svg">
                        <path d="m35.719,291.467c0,3.051 2.482,5.533 5.531,5.533h85.119c3.051,0 5.533-2.482 5.533-5.533v-5.64h-96.183v5.64z"/>
                        <path d="m113.441,48.091v-5.639c0-3.051-2.482-5.533-5.532-5.533h-48.2c-3.051,0-5.533,2.482-5.533,5.533v5.639 0.001l59.265-.001z"/>
                        <path d="m164.935,291.467c0,3.051 2.482,5.533 5.533,5.533h85.118c3.051,0 5.533-2.482 5.533-5.533v-5.64h-96.183v5.64z"/>
                        <path d="m242.658,5.532c0-3.051-2.482-5.532-5.532-5.532h-48.2c-3.05,0-5.532,2.482-5.532,5.532v5.64h59.263v-5.64z"/>
                        <path d="m278.614,264.503l-16.628-232.794c-0.205-2.881-2.629-5.138-5.517-5.138h-86.885c-2.888,0-5.312,2.257-5.517,5.137l-16.628,232.795c-0.111,1.548 0.411,3.028 1.469,4.164 1.057,1.136 2.496,1.762 4.048,1.762h120.141c1.552,0 2.991-0.626 4.048-1.762 1.059-1.136 1.58-2.615 1.469-4.164z"/>
                        <path d="m132.901,270.429l7.499-111.945-7.493-89.922c-0.237-2.844-2.658-5.072-5.513-5.072h-87.168c-2.854,0-5.275,2.229-5.513,5.073l-16.322,195.874c-0.13,1.561 0.382,3.054 1.442,4.206 1.06,1.152 2.506,1.786 4.072,1.786h108.996z"/>
                    </svg>
                </div>
                <h3 class="font-display text-2xl text-center font-semi-bold text-moss mb-2">Kualitas Jahitan Premium</h3>
                <p class="text-md text-text-dark/80">Bordir rapi, awet, dan detail. Menjaga tampilan selempang tetap elegan di setiap momen penting.</p>
            </div>

            <!-- Fast Production -->
            <div class="bg-white/90 p-8 rounded-xl shadow-md text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-leaf/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <!-- Stopwatch Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#344E41" viewBox="0 0 24 24">
                        <path d="M12 8a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0V9a1 1 0 0 1 1-1z"/>
                        <path d="M15.07 1H8.93a1 1 0 0 0 0 2h2.07v1.06A9 9 0 1 0 17.9 5.52a1 1 0 1 0-1.8.9A7 7 0 1 1 12 5a7.07 7.07 0 0 1 2.07.31A1 1 0 1 0 15.07 1z"/>
                    </svg>
                </div>
                <h3 class="font-display text-2xl text-center font-semi-bold text-moss mb-2">Proses Cepat & Tepat Waktu</h3>
                <p class="text-md text-text-dark/80">Pengerjaan tepat waktu tanpa mengurangi kualitas, cocok untuk kebutuhan mendadak dan acara resmi.</p>
            </div>

            <!-- Modern Machine -->
            <div class="bg-white/90 p-8 rounded-xl shadow-md text-center hover:shadow-lg transition-shadow">
                <div class="w-16 h-16 bg-leaf/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <!-- Mesin Bordir Icon -->
                    <svg width="36" height="36" viewBox="0 0 512 512" fill="#344E41" xmlns="http://www.w3.org/2000/svg" >
                        <path d="M478.609,392.437V139.13c0-46.03-37.448-83.478-83.478-83.478h-50.087V38.957c0-9.22-7.475-16.696-16.696-16.696 s-16.696,7.475-16.696,16.696v16.696h-33.391V38.957c0-9.22-7.475-16.696-16.696-16.696c-9.22,0-16.696,
                        7.475-16.696,16.696 v16.696h-128c-46.03,0-83.478,37.448-83.478,83.478v100.174c0,9.22,7.475,16.696,16.696,16.696h16.696v50.087 c0,9.22,7.475,16.696,16.696,16.696h16.696v16.696c0,9.22,7.475,16.696,16.696,16.696s16.696-7.475,
                        16.696-16.696v-16.696h16.696 c9.22,0,16.696-7.475,16.696-16.696V256h16.696c4.428,0,8.674-1.76,11.805-4.891c3.132-3.131,4.891-7.377,4.891-11.805v-50.087 h111.304v200.348H50.087C22.469,389.565,0,412.034,0,439.652v33.391c0,9.22,
                        7.475,16.696,16.696,16.696h478.609 c9.22,0,16.696-7.475,16.696-16.696v-33.391C512,417.887,498.041,399.329,478.609,392.437z M133.565,289.391h-33.391V256h33.391 V289.391z M395.13,189.217c9.22,0,16.696,7.475,16.696,16.696c0,9.22-7.475,
                        16.696-16.696,16.696s-16.696-7.475-16.696-16.696 C378.435,196.693,385.91,189.217,395.13,189.217z M395.13,256c9.22,0,16.696,7.475,16.696,16.696 c0,9.22-7.475,16.696-16.696,16.696s-16.696-7.475-16.696-16.696C378.435,263.475,385.91,
                        256,395.13,256z M478.609,456.348H33.391 v-16.696c0-9.206,7.49-16.696,16.696-16.696c16.559,0,395.834,0,411.826,0c9.206,0,16.696,7.49,16.696,16.696V456.348z"></path>
                    </svg>
                </div>
                <h3 class="font-display text-2xl text-center font-semi-bold text-moss mb-2">Teknologi Bordir Modern</h3>
                <p class="text-md text-text-dark/80">Dikerjakan dengan mesin bordir komputer presisi tinggi sehingga hasil setiap huruf dan pola tetap konsisten.</p>
            </div>

        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="bg-linear-to-b from-white/75 via-white/90 to-sage py-20 px-6 md:px-12 lg:px-16" id="contact">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <h2 class="font-display font-bold text-4xl md:text-4xl lg:text-5xl xl:text-5xl text-center text-moss mb-6">Where to Find Us</h2>
        <p class="text-md md:text-md lg:text-lg xl:text-lg text-center text-text-dark/80">
            Siap melayani dengan sepenuh hati. Silakan hubungi atau datang langsung ke toko kami.
        </p>

        <!-- Content -->
        <div class="mt-14 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <div class="bg-white/90 p-10 rounded-xl shadow-md">
                <h3 class="font-display text-3xl text-center font-bold text-moss">Wira Bordir Computer</h3>

                <div class="grid gap-4 text-md mt-8 text-text-dark">

                    <!-- ALAMAT -->
                    <div class="grid grid-cols-[40px_1fr] gap-3 items-start mb-2">
                        <span class="mt-1.5">
                            <!-- SVG ALAMAT -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="#344E41" viewBox="0 0 100 100" width="32" height="32">
                                <path d="M50.001 0C33.65 0 20.25 13.36 20.25 29.666c0 6.318 2.018 12.19 5.433 17.016L46.37 82.445c2.897 3.785 4.823 3.066 7.232-.2l22.818-38.83c.46-.834.822-1.722 1.137-2.629a29.28 29.28 0 0 0 2.192-11.12C79.75 13.36 66.354 0 50.001 0zm0 13.9c8.806 0 15.808 6.986 15.808 15.766c0 8.78-7.002 15.763-15.808 15.763c-8.805 0-15.81-6.982-15.81-15.763c0-8.78 7.005-15.765 15.81-15.765z" fill="#344E41"></path>
                            </svg>
                        </span>
                        <span class="leading-tight">
                            Tembung, Kec. Percut Sei Tuan, Kabupaten Deli Serdang, Sumatera Utara 20371, Indonesia
                        </span>
                    </div>

                    <!-- WHATSAPP -->
                    <div class="grid grid-cols-[40px_1fr] gap-3 items-start mb-2">
                        <span class="ml-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="#344E41" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M.057 24l1.687-6.163a11.867 11.867 0 01-1.587-5.96C.155 5.3 5.366 0 12.02 0c3.184 0 6.167 1.24 8.413 3.488a11.82 11.82 0 013.495 8.414c-.003 6.653-5.312 11.865-11.98 11.865a11.9 11.9 0 01-5.958-1.594L.057 24zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.593 5.448 0 9.886-4.434 9.889-9.877.003-5.462-4.415-9.89-9.881-9.893-5.46-.003-9.89 4.422-9.893 9.887a9.822 9.822 0 001.588 5.258l-.999 3.648 3.904-1.616zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.867-2.03-.967-.272-.099-.47-.148-.668.149-.198.297-.767.966-.94 1.164-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.149-.173.198-.297.297-.495.099-.198.05-.372-.025-.521-.074-.149-.668-1.611-.916-2.21-.242-.579-.487-.5-.668-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.262.489 1.694.625.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.007-1.413.248-.694.248-1.289.173-1.413z"/>
                            </svg>
                        </span>
                        <span class="leading-tight">WhatsApp : 0812-3456-7890</span>
                    </div>

                    <!-- JAM -->
                    <div class="grid grid-cols-[40px_1fr] gap-3 items-start">
                        <span class="mt-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none">
                                <path d="M12 7V12L9.5 13.5M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#344E41" stroke-width="2"/>
                            </svg>
                        </span>
                        <span class="leading-tight">
                            Senin - Jumat : 08.00 - 20.00 WIB <br>
                            Sabtu - Minggu : 09.00 - 18.00 WIB
                        </span>
                    </div>

                </div>
            </div>

            <!-- Google Maps -->
            <div class="relative rounded-xl overflow-hidden shadow-md h-[350px] md:h-[420px] lg:h-full">
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d248.8696047295145!2d98.75524042576026!3d3.607231165845517!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303136b1a0bb5349%3A0x9048080e5e3fe958!2sWira%20Bordir%20Computer!5e0!3m2!1sid!2sus!4v1764038135575!5m2!1sid!2sus" 
                    width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

        </div>
    </div>
</section>

<!-- Footer Section -->
<section class="bg-white/90 w-full py-12">
    <div class="mx-auto max-w-7xl px-6 md:px-12 lg:px-16">
        <div class="max-w-3xl mx-auto">       
            <div class="flex space-x-6 justify-center items-center mb-6">
                <a href="https://www.instagram.com/wirabordircomputer_?igsh=dm02OXplcTNlejNk" target="_blank" class="block  text-moss transition-all duration-500 hover:text-rose">
                    <svg class="w-7 h-7" viewBox="0 0 29 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.76556 14.8811C9.76556 12.3243 11.8389 10.2511 14.3972 10.2511C16.9555 10.2511 19.03 12.3243 19.03 14.8811C19.03 17.4379 16.9555 19.5111 14.3972 19.5111C11.8389 19.5111 9.76556 17.4379 9.76556 14.8811ZM7.26117 14.8811C7.26117 18.82 10.456 22.0129 14.3972 22.0129C18.3385 22.0129 21.5333 18.82 21.5333 14.8811C21.5333 10.9422 18.3385 7.7493 14.3972 7.7493C10.456 7.7493 7.26117 10.9422 7.26117 14.8811ZM20.1481 7.46652C20.148 7.79616 20.2457 8.11843 20.4288 8.39258C20.6119 8.66674 20.8723 8.88046 21.177 9.00673C21.4817 9.133 21.8169 9.16614 22.1405 9.10196C22.464 9.03778 22.7612 8.87916 22.9945 8.64617C23.2278 8.41318 23.3868 8.11627 23.4513 7.79299C23.5157 7.46972 23.4829 7.13459 23.3568 6.83C23.2307 6.5254 23.017 6.26502 22.7428 6.08178C22.4687 5.89853 22.1463 5.80065 21.8164 5.80052H21.8158C21.3737 5.80073 20.9497 5.9763 20.637 6.28867C20.3243 6.60104 20.1485 7.02467 20.1481 7.46652ZM8.78274 26.1863C7.42782 26.1246 6.69138 25.8991 6.20197 25.7085C5.55314 25.4561 5.0902 25.1554 4.60346 24.6696C4.11672 24.1839 3.81543 23.7216 3.56395 23.0732C3.37317 22.5843 3.14748 21.8481 3.08588 20.494C3.01851 19.03 3.00506 18.5902 3.00506 14.8812C3.00506 11.1722 3.01962 10.7336 3.08588 9.26841C3.14759 7.9143 3.37495 7.17952 3.56395 6.68919C3.81654 6.04074 4.11739 5.57808 4.60346 5.09163C5.08953 4.60519 5.55203 4.30408 6.20197 4.05274C6.69116 3.86208 7.42782 3.63652 8.78274 3.57497C10.2476 3.50763 10.6877 3.49419 14.3972 3.49419C18.1068 3.49419 18.5473 3.50874 20.0134 3.57497C21.3683 3.63663 22.1035 3.86385 22.5941 4.05274C23.243 4.30408 23.7059 4.60585 24.1926 5.09163C24.6794 5.57741 24.9796 6.04074 25.2322 6.68919C25.4229 7.17808 25.6486 7.9143 25.7102 9.26841C25.7776 10.7336 25.7911 11.1722 25.7911 14.8812C25.7911 18.5902 25.7776 19.0287 25.7102 20.494C25.6485 21.8481 25.4217 22.5841 25.2322 23.0732C24.9796 23.7216 24.6787 24.1843 24.1926 24.6696C23.7066 25.155 23.243 25.4561 22.5941 25.7085C22.105 25.8992 21.3683 26.1247 20.0134 26.1863C18.5485 26.2536 18.1084 26.2671 14.3972 26.2671C10.686 26.2671 10.2472 26.2536 8.78274 26.1863ZM8.66768 1.0763C7.18823 1.14363 6.17729 1.37808 5.29443 1.72141C4.3801 2.07597 3.60608 2.55163 2.83262 3.32341C2.05916 4.09519 1.58443 4.86997 1.22966 5.78374C0.88612 6.66663 0.651535 7.67641 0.584162 9.15497C0.515676 10.6359 0.5 11.1093 0.5 14.8811C0.5 18.6529 0.515676 19.1263 0.584162 20.6072C0.651535 22.0859 0.88612 23.0955 1.22966 23.9784C1.58443 24.8916 2.05927 25.6673 2.83262 26.4387C3.60597 27.2102 4.3801 27.6852 5.29443 28.0407C6.17896 28.3841 7.18823 28.6185 8.66768 28.6859C10.1502 28.7532 10.6232 28.77 14.3972 28.77C18.1713 28.77 18.645 28.7543 20.1268 28.6859C21.6063 28.6185 22.6166 28.3841 23.5 28.0407C24.4138 27.6852 25.1884 27.2105 25.9618 26.4387C26.7353 25.667 27.209 24.8916 27.5648 23.9784C27.9083 23.0955 28.144 22.0857 28.2103 20.6072C28.2777 19.1252 28.2933 18.6529 28.2933 14.8811C28.2933 11.1093 28.2777 10.6359 28.2103 9.15497C28.1429 7.6763 27.9083 6.66608 27.5648 5.78374C27.209 4.87052 26.7341 4.09641 25.9618 3.32341C25.1896 2.55041 24.4138 2.07597 23.5011 1.72141C22.6166 1.37808 21.6062 1.14252 20.1279 1.0763C18.6461 1.00897 18.1724 0.992188 14.3983 0.992188C10.6243 0.992188 10.1502 1.00785 8.66768 1.0763Z" fill="currentColor"/>
                    </svg>
                </a>
                <a href="https://wa.me/6282373795900" target="_blank" class="block  text-moss transition-all duration-500 hover:text-rose">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.04 2C6.53 2 2 6.48 2 11.97c0 2.1.61 4.05 1.67 5.73L2 22l4.42-1.61c1.62.89 3.49 1.39 5.62 1.39 5.51 0 10.04-4.48 10.04-9.97C22.08 6.48 17.55 2 12.04 2zm5.82 14.43c-.24.67-1.39 1.31-1.92 1.38-.49.07-1.11.1-1.79-.11-.41-.13-.94-.31-1.62-.61-2.85-1.24-4.71-4.11-4.85-4.31-.14-.19-1.16-1.54-1.16-2.94 0-1.41.73-2.1 1-2.39.27-.29.59-.36.79-.36h.57c.19 0 .44-.07.69.53.24.6.82 2.07.9 2.22.07.15.12.33.02.52-.1.19-.15.33-.29.51-.14.17-.3.39-.43.52-.14.14-.29.29-.13.57.15.29.66 1.09 1.42 1.77.98.87 1.8 1.14 2.08 1.28.27.13.44.11.6-.07.17-.19.69-.8.88-1.07.19-.29.37-.23.62-.14.24.1 1.53.72 1.79.85.27.14.44.2.51.31.07.12.07.67-.17 1.35z" fill="currentColor"/>
                    </svg>
                </a>
            </div>
            <span class="text-md text-text-dark text-center block">© <a href="{{ route('home') }}" class="hover:text-rose">Wira Bordir Computer</a> 2025, All rights reserved.</span>
        </div>
    </div>
</section>

{{-- ========================== --}}
{{--          /STYLES           --}}
{{-- ========================== --}}
@push('styles')
    
@endpush

{{-- ========================== --}}
{{--          /SCRIPTS          --}}
{{-- ========================== --}}
@push('scripts')
    <script>
        let lastScrollY = window.scrollY;
        let hideTimeout;

        window.addEventListener("scroll", () => {
            const navbar = document.getElementById("navbar");

            // Jika user kembali ke paling atas (hero)
            if (window.scrollY <= 10) {
                clearTimeout(hideTimeout);
                navbar.classList.remove("-translate-y-full", "opacity-0");
                return; // Stop di sini
            }

            // Scroll ke bawah
            if (window.scrollY > lastScrollY) {
                clearTimeout(hideTimeout);

                // Delay sebelum hide
                hideTimeout = setTimeout(() => {
                    navbar.classList.add("-translate-y-full", "opacity-0");
                }, 1500);

            } else {
                // Scroll ke atas → tampil
                navbar.classList.remove("-translate-y-full", "opacity-0");
            }

            lastScrollY = window.scrollY;
        });
    </script>
@endpush

@endsection
