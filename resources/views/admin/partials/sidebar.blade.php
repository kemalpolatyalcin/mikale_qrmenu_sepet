<aside class="w-64 bg-white shadow-xl hidden md:flex flex-col justify-between z-20 shrink-0">
    <div>
        <div class="h-24 flex items-center justify-center border-b border-gray-100 mb-6 px-4">
            @if(isset($activeRestaurant) && $activeRestaurant->logo_url)
                <img src="{{ asset($activeRestaurant->logo_url) }}" class="h-12 object-contain" alt="Logo">
            @else
                <span class="text-lg font-bold tracking-widest text-[#1C1C1C] sidebar-restaurant-name">{{ isset($activeRestaurant) ? $activeRestaurant->name : ($siteSettings['restaurant_name'] ?? 'MIKALE') }}</span>
            @endif
        </div>
        <nav class="px-4 space-y-2">
            @if((session('is_developer') || (auth()->check() && auth()->user() && auth()->user()->email === 'mikale@gmail.com')) && isset($restaurantsList) && $restaurantsList->count() > 0)
            <div class="mb-4">
                <label class="text-[10px] uppercase font-bold text-gray-400 tracking-wider block mb-1.5 px-2">Restoranlar</label>
                <div class="space-y-1 max-h-32 overflow-y-auto border border-gray-100 rounded-xl p-1.5 bg-gray-50/50">
                    @foreach($restaurantsList as $res)
                        <form action="{{ route('admin.restaurants.select') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="restaurant_id" value="{{ $res->id }}">
                            <button type="submit" class="w-full text-left px-3 py-1.5 rounded-lg text-xs font-semibold transition-all {{ (isset($activeRestaurant) && $activeRestaurant->id == $res->id) ? 'bg-[#8C6C47] text-white shadow-sm font-bold' : 'text-gray-600 hover:bg-gray-100' }}">
                                {{ $res->name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
            @endif
            <a href="{{ url('/') }}" target="_blank"
                class="flex items-center gap-3 px-4 py-3 text-[#8C6C47] bg-amber-50 rounded-xl mb-4 border border-amber-100">
                <i class="fa-solid fa-arrow-up-right-from-square w-5 text-center"></i> <span>Menüyü Gör</span>
            </a>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-chart-pie w-5 text-center"></i> <span>Gösterge Paneli</span>
            </a>
            <a href="{{ route('admin.categories') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.categories') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-layer-group w-5 text-center"></i> <span>Kategoriler</span>
            </a>
            <a href="{{ route('admin.products') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.products') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-utensils w-5 text-center"></i> <span>Ürünler</span>
            </a>
            <a href="{{ route('admin.orders') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.orders') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-table w-5 text-center"></i> <span>Masalar</span>
            </a>
            <a href="{{ route('admin.register') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.register') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-cash-register w-5 text-center"></i> <span>Kasa</span>
            </a>
            <a href="{{ route('admin.tables') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.tables') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-qrcode w-5 text-center"></i> <span>Masalar ve QR</span>
            </a>
            @if(session('is_developer') || (auth()->check() && auth()->user() && auth()->user()->email === 'mikale@gmail.com'))
                <a href="{{ route('admin.developer.restaurants') }}"
                    class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.developer.restaurants') ? 'bg-[#8C6C47] text-white shadow-md' : 'text-gray-600 hover:bg-gray-50 hover:text-[#8C6C47]' }} rounded-xl font-medium text-sm transition-all">
                    <i class="fa-solid fa-hotel w-5 text-center"></i> <span>Restoranlar</span>
                </a>
            @endif
            <a href="{{ route('admin.settings') }}"
                class="flex items-center gap-3 px-4 py-3 {{ request()->routeIs('admin.settings') ? 'bg-[#8C6C47] text-white' : 'text-gray-600 hover:bg-gray-50' }} rounded-xl transition-all">
                <i class="fa-solid fa-gear w-5 text-center"></i> <span>Ayarlar</span>
            </a>
        </nav>
    </div>
    <div class="p-4 border-t border-gray-100">
        <form action="{{ route('logout') }}" method="POST" class="mb-4">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition-all">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i> Çıkış Yap
            </button>
        </form>
        <div class="text-center text-[10px] text-gray-400 font-medium tracking-wide">
            Powered by <strong class="text-gray-600">Mikale QR</strong> v1.0
        </div>
    </div>
</aside>

<script>
    (function() {
        const token = localStorage.getItem('admin_token');
        if (token) {
            const originalFetch = window.fetch;
            window.fetch = async function(url, options = {}) {
                options.headers = options.headers || {};
                if (!options.headers['Authorization']) {
                    options.headers['Authorization'] = `Bearer ${token}`;
                }
                options.headers['Accept'] = 'application/json';
                const response = await originalFetch(url, options);
                if (response.status === 401) {
                    localStorage.removeItem('admin_token');
                    window.location.href = '/login';
                }
                return response;
            };
        }

        document.addEventListener('submit', async function(e) {
            const form = e.target;
            if (form && form.action && form.action.endsWith('/logout')) {
                const token = localStorage.getItem('admin_token');
                if (token) {
                    e.preventDefault();
                    try {
                        await fetch('/api/admin/logout', {
                            method: 'POST',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'Accept': 'application/json'
                            }
                        });
                    } catch (err) {}
                    localStorage.removeItem('admin_token');
                    document.cookie = "admin_token=; path=/; expires=Thu, 01 Jan 1970 00:00:00 UTC;";
                    form.submit();
                }
            }
        });
    })();
</script>