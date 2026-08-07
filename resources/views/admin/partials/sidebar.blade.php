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
            <a href="https://www.google.com/search?client=firefox-b-d&hs=rIMq&sa=X&sca_esv=3e69f37c1e17d2ac&sxsrf=APpeQntEY5wgTD9sHwOeaN0Jq8LXoxnKLA:1785492779306&q=%C3%96ztaylan+S%C3%BCtevi+Yorumlar&rflfq=1&num=20&stick=H4sIAAAAAAAAAONgkxI2tLQ0MTM2sLAwMjQ3MjAHwQ2MjK8YpQ5PqypJrMxJzFMIPrynJLUsUyEyv6g0NyexaBErHkkA07rjC1QAAAA&rldimm=1994630882172070707&tbm=lcl&hl=tr-TR&ved=2ahUKEwiV3u_21vyVAxXc1gIHHaC3MwkQ9fQKegQIORAG&biw=630&bih=1056&dpr=0.88#lkt=LocalPoiReviews" target="_blank"
                class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl transition-all">
                <i class="fa-solid fa-star w-5 text-center text-amber-500"></i> <span>Google Yorumları</span>
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

<div id="admin-notif-container" class="fixed top-3 right-16 md:top-5 md:right-[220px] z-[60] font-poppins">
    <button id="admin-notif-btn" onclick="toggleAdminNotif()" class="relative w-10 h-10 md:w-11 md:h-11 rounded-full bg-white shadow-md border border-gray-100 hover:border-[#8C6C47] text-gray-700 flex items-center justify-center transition-all hover:scale-105">
        <i class="fa-solid fa-bell text-lg md:text-xl"></i>
        <span id="admin-notif-badge" class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-[9px] font-extrabold w-5 h-5 flex items-center justify-center border-2 border-white hidden">0</span>
    </button>

    <div id="admin-notif-panel" class="absolute right-0 mt-3 w-80 md:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 hidden flex-col overflow-hidden transform scale-95 opacity-0 origin-top-right transition-all duration-200 z-[200]">
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-envelope text-[#8C6C47]"></i>
                <span>Mesaj Kutusu</span>
            </h3>
            <button onclick="markAllNotifAsRead()" class="text-xs text-[#8C6C47] hover:underline font-semibold">Tümünü Oku</button>
        </div>
        <div id="admin-notif-list" class="max-h-[350px] overflow-y-auto divide-y divide-gray-50 no-scrollbar">
            <div class="p-6 text-center text-gray-400 text-xs">
                <i class="fa-solid fa-spinner animate-spin text-base mb-2 block"></i>
                Yükleniyor...
            </div>
        </div>
    </div>
</div>

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

        if (window.location.pathname.indexOf('/admin/orders') === -1) {
            function playAdminChime() {
                try {
                    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc1 = audioCtx.createOscillator();
                    const osc2 = audioCtx.createOscillator();
                    const gainNode = audioCtx.createGain();
                    
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                    osc1.frequency.exponentialRampToValueAtTime(659.25, audioCtx.currentTime + 0.15);
                    osc1.frequency.exponentialRampToValueAtTime(783.99, audioCtx.currentTime + 0.3);
                    
                    osc2.type = 'triangle';
                    osc2.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                    osc2.frequency.exponentialRampToValueAtTime(783.99, audioCtx.currentTime + 0.3);
                    
                    gainNode.gain.setValueAtTime(0.15, audioCtx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.4);
                    
                    osc1.connect(gainNode);
                    osc2.connect(gainNode);
                    gainNode.connect(audioCtx.destination);
                    
                    osc1.start();
                    osc2.start();
                    osc1.stop(audioCtx.currentTime + 0.4);
                    osc2.stop(audioCtx.currentTime + 0.4);
                } catch (e) {
                    console.error(e);
                }
            }

            function showAdminOrderNotification(tableNumber, description) {
                playAdminChime();
                
                let container = document.getElementById('admin-notification-container');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'admin-notification-container';
                    container.className = 'fixed top-6 right-6 z-[9999] flex flex-col gap-3 max-w-sm w-full pointer-events-none';
                    document.body.appendChild(container);
                }
                
                const notification = document.createElement('div');
                notification.className = 'pointer-events-auto bg-white border-l-4 border-amber-500 shadow-2xl rounded-2xl p-4 flex justify-between items-start gap-4 transition-all duration-300 transform translate-x-full opacity-0 cursor-pointer hover:bg-gray-50/80';
                
                const titleText = tableNumber ? `Masa ${tableNumber} - Yeni Sipariş!` : 'Yeni Sipariş Alındı!';
                const bodyText = description ? description : 'Sipariş detayları güncellendi. Kontrol etmek için tıklayın.';
                
                notification.innerHTML = `
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                            <i class="fa-solid fa-bell text-lg"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">${titleText}</h4>
                            <p class="text-xs text-gray-500 mt-1">${bodyText}</p>
                        </div>
                    </div>
                    <button class="text-gray-400 hover:text-gray-600 transition-colors text-sm font-bold bg-gray-50 hover:bg-gray-100 p-1.5 rounded-lg border border-gray-200">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                
                container.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.remove('translate-x-full', 'opacity-0');
                }, 50);
                
                notification.addEventListener('click', () => {
                    window.location.href = '/admin/orders';
                });
                
                const closeBtn = notification.querySelector('button');
                closeBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (notification.parentNode) {
                        notification.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => notification.remove(), 300);
                    }
                });
                
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.classList.add('translate-x-full', 'opacity-0');
                        setTimeout(() => notification.remove(), 300);
                    }
                }, 6000);
            }

            setInterval(() => {
                fetch('/admin/api/new-orders-check')
                    .then(res => res.json())
                    .then(data => {
                        if (data && data.has_new) {
                            showAdminOrderNotification(data.table_number, data.description);
                        }
                    })
                    .catch(() => {});
            }, 3000);
        }

        let localNotifs = [];
        let maxSeenId = parseInt(localStorage.getItem('admin_max_seen_id') || '0');

        window.injectNotificationButton = function() {
            const notifContainer = document.getElementById('admin-notif-container');
            if (!notifContainer) return;

            const placeholder = document.getElementById('desktop-notif-placeholder');
            if (placeholder) {
                if (notifContainer.parentNode !== placeholder) {
                    notifContainer.className = 'relative font-poppins shrink-0';
                    placeholder.appendChild(notifContainer);
                }
                return;
            }

            const isDesktop = window.innerWidth >= 768;
            let profileEl = null;

            if (isDesktop) {
                const desktopHeaders = document.querySelectorAll('header:not(.md\\:hidden)');
                for (let h of desktopHeaders) {
                    let el = h.querySelector('img.h-10, div.w-10.h-10, img[alt*="Logo"], img[alt*="logo"]');
                    if (el) {
                        profileEl = el;
                        break;
                    }
                }
            } else {
                const mobileHeader = document.querySelector('header.md\\:hidden');
                if (mobileHeader) {
                    profileEl = mobileHeader.querySelector('img.h-10, img[alt*="Logo"], img[alt*="logo"]');
                }
            }

            if (profileEl) {
                const parent = profileEl.parentNode;
                if (parent.tagName === 'HEADER' && parent.classList.contains('justify-between')) {
                    let wrap = parent.querySelector('.mobile-right-group');
                    if (!wrap) {
                        wrap = document.createElement('div');
                        wrap.className = 'flex items-center gap-3 mobile-right-group';
                        parent.insertBefore(wrap, profileEl);
                        wrap.appendChild(profileEl);
                    }
                    if (notifContainer.parentNode !== wrap) {
                        notifContainer.className = 'relative font-poppins shrink-0';
                        wrap.insertBefore(notifContainer, profileEl);
                    }
                } else {
                    if (notifContainer.parentNode !== parent) {
                        notifContainer.className = 'relative font-poppins shrink-0 mr-2 md:mr-3';
                        parent.insertBefore(notifContainer, profileEl);
                    }
                }
            } else {
                let target = null;
                const managerHeader = document.querySelector('header div.flex.items-center.gap-3.w-full') || 
                                      document.querySelector('header div.flex.items-center.gap-3');
                if (managerHeader) {
                    target = managerHeader;
                }
                if (!target) {
                    const headers = document.querySelectorAll('header');
                    headers.forEach(h => {
                        if (!h.classList.contains('md:hidden')) {
                            const groups = h.querySelectorAll('div.flex.items-center');
                            if (groups.length > 0) {
                                target = groups[groups.length - 1];
                            }
                        }
                    });
                }
                if (!target) {
                    const mobileHeader = document.querySelector('header.md\\:hidden');
                    if (mobileHeader) {
                        target = mobileHeader;
                    }
                }
                if (target && notifContainer.parentNode !== target) {
                    notifContainer.className = 'relative font-poppins shrink-0 ml-2 md:ml-3';
                    target.appendChild(notifContainer);
                }
            }
        };

                window.toggleAdminNotif = function() {
            const panel = document.getElementById('admin-notif-panel');
            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                panel.classList.add('flex');
                setTimeout(() => {
                    panel.classList.remove('scale-95', 'opacity-0');
                    panel.classList.add('scale-100', 'opacity-100');
                }, 10);
                loadNotifications();
            } else {
                panel.classList.remove('scale-100', 'opacity-100');
                panel.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    panel.classList.remove('flex');
                    panel.classList.add('hidden');
                }, 200);
            }
        };

        window.loadNotifications = function() {
            fetch('/admin/api/notifications')
                .then(res => res.json())
                .then(data => {
                    localNotifs = data;
                    renderNotifications();
                });
        };

        window.renderNotifications = function() {
            window.injectNotificationButton();
            const list = document.getElementById('admin-notif-list');
            if (localNotifs.length === 0) {
                list.innerHTML = `<div class="p-8 text-center text-gray-400 text-xs">Mesaj bulunmuyor.</div>`;
                return;
            }

            let html = '';
            localNotifs.forEach(n => {
                const isUnread = n.id > maxSeenId;
                const unreadClass = isUnread ? 'bg-amber-50/20 font-semibold' : '';
                const bg = n.is_waiter_call ? 'bg-rose-50/40 hover:bg-rose-50/60' : 'bg-white hover:bg-gray-50';
                const iconColor = n.is_waiter_call ? 'text-red-500 bg-red-50' : 'text-green-500 bg-green-50';
                const icon = n.is_waiter_call ? 'fa-bell-concierge' : 'fa-basket-shopping';
                const title = n.is_waiter_call ? `${n.table_number} - Garson Çağırıyor` : `${n.table_number} - Yeni Sipariş`;
                const desc = n.is_waiter_call ? (n.order_note || 'Garson çağrısı iletildi.') : n.items_summary;
                const price = n.is_waiter_call ? '' : `<span class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded-full">₺${n.total_amount}</span>`;

                html += `
                    <div class="p-4 flex gap-3 transition-colors ${bg} ${unreadClass}">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 text-base ${iconColor}">
                            <i class="fa-solid ${icon}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start gap-2 mb-1">
                                <h4 class="text-xs font-bold text-gray-900 truncate">${title}</h4>
                                <span class="text-[10px] text-gray-400 whitespace-nowrap">${n.created_at}</span>
                            </div>
                            <p class="text-xs text-gray-500 line-clamp-2">${desc}</p>
                            <div class="mt-2 flex items-center justify-between">
                                ${price}
                            </div>
                        </div>
                    </div>
                `;
            });
            list.innerHTML = html;
        };

        window.markAllNotifAsRead = function() {
            if (localNotifs.length > 0) {
                const maxId = Math.max(...localNotifs.map(n => n.id));
                if (maxId > maxSeenId) {
                    maxSeenId = maxId;
                    localStorage.setItem('admin_max_seen_id', maxSeenId.toString());
                    updateUnreadCount();
                    renderNotifications();
                }
            }
        };

        window.updateUnreadCount = function() {
            window.injectNotificationButton();
            const badge = document.getElementById('admin-notif-badge');
            const unreadCount = localNotifs.filter(n => n.id > maxSeenId).length;
            if (badge) {
                if (unreadCount > 0) {
                    badge.innerText = unreadCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        };

        function playDingSound() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, audioCtx.currentTime);
                gain1.gain.setValueAtTime(0.15, audioCtx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);
                osc1.start(audioCtx.currentTime);
                osc1.stop(audioCtx.currentTime + 0.6);

                setTimeout(() => {
                    const osc2 = audioCtx.createOscillator();
                    const gain2 = audioCtx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(audioCtx.destination);
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880.00, audioCtx.currentTime);
                    gain2.gain.setValueAtTime(0.15, audioCtx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.8);
                    osc2.start(audioCtx.currentTime);
                    osc2.stop(audioCtx.currentTime + 0.8);
                }, 120);
            } catch (e) {}
        }

        window.pollNotifications = function() {
            window.injectNotificationButton();
            fetch('/admin/api/notifications')
                .then(res => res.json())
                .then(data => {
                    if (data.length > 0) {
                        const maxId = Math.max(...data.map(n => n.id));
                        if (maxId > maxSeenId) {
                            if (localNotifs.length > 0) {
                                playDingSound();
                            }
                        }
                        localNotifs = data;
                        updateUnreadCount();
                        const panel = document.getElementById('admin-notif-panel');
                        if (panel && !panel.classList.contains('hidden')) {
                            renderNotifications();
                        }
                    }
                })
                .catch(() => {});
        };

        document.addEventListener('click', function(e) {
            const panel = document.getElementById('admin-notif-panel');
            const btn = document.getElementById('admin-notif-btn');
            if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
                if (!panel.classList.contains('hidden')) {
                    panel.classList.remove('scale-100', 'opacity-100');
                    panel.classList.add('scale-95', 'opacity-0');
                    setTimeout(() => {
                        panel.classList.remove('flex');
                        panel.classList.add('hidden');
                    }, 200);
                }
            }
        });

        window.injectNotificationButton();
        document.addEventListener('DOMContentLoaded', window.injectNotificationButton);
        window.addEventListener('load', window.injectNotificationButton);
        window.addEventListener('resize', window.injectNotificationButton);
        setTimeout(window.injectNotificationButton, 200);
        setTimeout(window.injectNotificationButton, 1000);
        setTimeout(window.injectNotificationButton, 3000);

        setInterval(window.pollNotifications, 5000);
        setTimeout(window.pollNotifications, 500);
    })();
</script>
