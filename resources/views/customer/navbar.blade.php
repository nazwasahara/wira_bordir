<nav id="navbar" x-data="{ open: false }"
    class="font-sans fixed top-6 left-0 w-full z-50 px-6 md:px-12 lg:px-16 transition-all duration-500 ease-in-out">

    <div class="max-w-7xl mx-auto backdrop-blur-lg bg-white/50 shadow-xl rounded-2xl py-2">
        <!-- Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ url('/') }}" class="flex items-center">
                        <img src="{{ asset('images/logo-wbc.png') }}" alt="Logo WBC" class="h-15 w-15">
                    </a>
                
                    <!-- Left Menu (Navigation) -->
                    <div class="hidden space-x-10 sm:ms-8 sm:flex font-semibold uppercase tracking-wider">
                        <a href="{{ url('/') }}"
                            class="hover:text-rose transition-colors {{ request()->is('/') ? 'text-xl text-rose' : 'text-lg text-text-dark' }}">
                            Home
                        </a>
                        <a href="{{ route('gallery') }}"
                            class="hover:text-rose transition-colors {{ request()->routeIs('gallery') ? 'text-xl text-rose' : 'text-lg text-text-dark' }}">
                            Gallery
                        </a>
                    </div>
                </div>
        
                <!-- Right Menu (Auth) -->
                <div class="hidden space-x-10 sm:ms-8 sm:flex font-semibold uppercase tracking-wider">
                    @guest
                        <a href="{{ route('login') }}"
                        class="hover:text-rose transition-colors {{ request()->routeIs('login') ? 'text-xl text-rose' : 'text-lg text-text-dark' }}">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                        class="hover:text-rose transition-colors mr-3 {{ request()->routeIs('register') ? 'text-xl text-rose' : 'text-lg text-text-dark' }}">
                            Register
                        </a>
                    @endguest

                    @auth
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <!-- Username -->
                        <button @click="dropdownOpen = !dropdownOpen" 
                                class="flex items-center space-x-1 hover:text-rose transition-colors text-lg text-text-dark">
                            <span>{{ Auth::user()->username }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': dropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" 
                            class="absolute right-0 mt-4 w-52 bg-white shadow-xl rounded-2xl py-1 z-50">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left gap-2 px-4 py-2 text-text-dark hover:text-rose rounded-2xl uppercase">
                                    <!-- Icon Logout -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                                    </svg>
                                    <span>Logout</span>
                                </button>
                            </form>

                            <!-- Riwayat Pesanan -->
                            <a href="{{ route('order.history') }}" 
                            class="flex items-center w-full text-left gap-2 px-4 py-2 text-text-dark hover:text-rose rounded-2xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 25 25" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1.68 9.46A2 2 0 0115.34 22H8.66a2 2 0 01-1.98-1.54L5 11z" />
                                </svg>
                                <span>Order History</span>
                            </a>
                        </div>
                    </div>
                    @endauth
                </div>

                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button @click="open = !open" 
                            class="inline-flex items-center justify-center p-2 text-text-dark focus:outline-none transition duration-150 ease-in-out">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" class="sm:hidden px-6 pb-4 space-y-4 font-semibold tracking-wide">
            <!-- Navigation Links -->
            <a href="{{ url('/') }}" 
            class="block text-lg mt-4 {{ request()->is('/') ? 'text-lg text-rose' : 'text-text-dark' }} hover:text-rose uppercase transition-colors">
                Home
            </a>
            <a href="{{ route('gallery') }}" 
            class="block text-lg mt-2 {{ request()->routeIs('gallery') ? 'text-lg text-rose' : 'text-text-dark' }} hover:text-rose uppercase transition-colors">
                Gallery
            </a>

            @guest
                <a href="{{ route('login') }}" class="block text-lg mt-8 text-text-dark hover:text-rose uppercase transition-colors">Login</a>
                <a href="{{ route('register') }}" class="block text-lg mt-2 text-text-dark hover:text-rose uppercase transition-colors">Register</a>
            @endguest

            @auth
                <!-- Username -->
                <div class="mt-4">
                    <span class="block text-lg mt-8 text-text-dark">{{ Auth::user()->username }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="flex items-center w-full text-left py-2 text-lg text-text-dark hover:text-rose rounded-2xl space-x-2 uppercase transition-colors">
                            <!-- Icon Logout -->
                            <span>Logout</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1" />
                            </svg>
                        </button>
                    </form>
                    <a href="{{ route('order.history') }}" class="flex items-center w-full text-left py-2 text-lg text-text-dark hover:text-rose rounded-2xl space-x-2 uppercase transition-colors">
                        <span>Order History</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 25 25" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 10-8 0v4M5 11h14l-1.68 9.46A2 2 0 0115.34 22H8.66a2 2 0 01-1.98-1.54L5 11z" />
                        </svg>
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
