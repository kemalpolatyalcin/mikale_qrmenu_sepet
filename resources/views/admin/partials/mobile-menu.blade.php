<div id="mobile-admin-menu"
    class="fixed inset-0 z-50 transform -translate-x-full transition-transform duration-300 md:hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        onclick="document.getElementById('mobile-admin-menu').classList.add('-translate-x-full')"></div>
    <div class="absolute top-0 left-0 w-64 h-full bg-white shadow-xl flex flex-col justify-between py-6 px-4">
        <div>
            <div class="flex justify-between items-center mb-6 px-2 border-b border-gray-100 pb-4">
                @if(isset($activeRestaurant) && $activeRestaurant->logo_url)
                    <img src="{{ asset($activeRestaurant->logo_url) }}" class="h-10 object-contain" alt="Logo">
                @else
                    <img src="{{ asset('images/oztaylan_logo.jpg') }}" class="h-10 object-contain mix-blend-multiply" alt="Logo">
                @endif
                <button onclick="document.getElementById('mobile-admin-menu').classList.add('-translate-x-full')"
                    class="text-gray-500 text-xl focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
            </div>

            <nav class="space-y-3">
                @if((session('is_developer') || (auth()->check() && auth()->user() && auth()->user()->email === 'mikale@gmail.com')) && isset($restaurantsList) && $restaurantsList->count() > 0)
                <div class="mb-4">
                    <label class="text-[9px] uppercase font-bold text-gray-400 tracking-wider block mb-1.5 px-2">Restoranlar</label>
                    <div class="space-y-1 max-h-32 overflow-y-auto border border-gray-100 rounded-xl p-1.5 bg-gray-50/50">
                        @foreach($restaurantsList as $res)
                            <form action="{{ route('admin.restaurants.select') }}" method="POST" class="m-0">
                                @csrf
                                <input type="hidden" name="restaurant_id" value="{{ $res->id }}">
                                <button type="submit" class="w-full text-left px-3 py-1.5 rounded-lg text-[11px] font-semibold transition-all {{ (isset($activeRestaurant) && $activeRestaurant->id == $res->id) ? 'bg-[#8C6C47] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    {{ $res->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
                @endif
                <a href="{{ url('/') }}" target="_blank"
                    class="flex items-center gap-3 px-4 py-3 text-[#8C6C47] bg-amber-50 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i> Menüyü Görüntüle
                </a>

                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-chart-pie w-5 text-center"></i> Gösterge Paneli
                </a>
                <a href="{{ route('admin.categories') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-layer-group w-5 text-center"></i> Kategoriler
                </a>
                <a href="{{ route('admin.products') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.products') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-utensils w-5 text-center"></i> Ürünler
                </a>
                <a href="{{ route('admin.orders') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.orders') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-table w-5 text-center"></i> Masalar
                </a>
                <a href="{{ route('admin.register') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.register') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-cash-register w-5 text-center"></i> Kasa
                </a>
                <a href="{{ route('admin.tables') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tables') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-qrcode w-5 text-center"></i> Masalar ve QR
                </a>
                @if(session('is_developer') || (auth()->check() && auth()->user() && auth()->user()->email === 'mikale@gmail.com'))
                    <a href="{{ route('admin.developer.restaurants') }}"
                        class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.developer.restaurants') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                        <i class="fa-solid fa-hotel w-5 text-center"></i> Restoranlar
                    </a>
                @endif
                <a href="{{ route('admin.settings') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#8C6C47] text-white shadow-md mt-4' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47] mt-4' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-gear w-5 text-center"></i> Ayarlar
                </a>
                <a href="https://www.google.com/search?client=firefox-b-d&hs=rIMq&sa=X&sca_esv=3e69f37c1e17d2ac&sxsrf=APpeQntEY5wgTD9sHwOeaN0Jq8LXoxnKLA:1785492779306&q=%C3%96ztaylan+S%C3%BCtevi+Yorumlar&rflfq=1&num=20&stick=H4sIAAAAAAAAAONgkxI2tLQ0MTM2sLAwMjQ3MjAHwQ2MjK8YpQ5PqypJrMxJzFMIPrynJLUsUyEyv6g0NyexaBErHkkA07rjC1QAAAA&rldimm=1994630882172070707&tbm=lcl&hl=tr-TR&ved=2ahUKEwiV3u_21vyVAxXc1gIHHaC3MwkQ9fQKegQIORAG&biw=630&bih=1056&dpr=0.88#lkt=LocalPoiReviews" target="_blank"
                    class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47] rounded-xl font-medium text-sm transition-all mt-3">
                    <i class="fa-solid fa-star w-5 text-center text-amber-500"></i> Google Yorumları
                </a>
            </nav>
        </div>

        <div class="mt-4 border-t border-gray-100 pt-4">
            <form action="{{ route('logout') }}" method="POST" class="mb-4">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Çıkış Yap
                </button>
            </form>
            <div class="text-center text-[9px] text-gray-400 font-medium tracking-wide">
                Powered by <strong class="text-gray-600">Mikale QR Menu</strong>
            </div>
        </div>
    </div>
</div>