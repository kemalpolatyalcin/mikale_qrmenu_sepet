<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mikale | Yönetim Paneli</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Allison&family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        .font-allison {
            font-family: 'Allison', cursive;
        }

        .font-poppins {
            font-family: 'Poppins', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-[#F9F9F9] font-poppins flex h-screen w-full overflow-hidden">

    @include('admin.partials.sidebar')

    <div class="flex-1 flex flex-col h-screen min-w-0 bg-[#F9F9F9]">

        <header
            class="md:hidden h-16 w-full bg-white border-b border-gray-100 px-6 flex items-center justify-between shrink-0 z-20">
            <button id="mobileMenuBtn" onclick="document.getElementById('mobile-admin-menu').classList.remove('-translate-x-full')" class="text-gray-800 p-2 hover:bg-gray-50 rounded-lg transition-colors">
                <i class="fa-solid fa-bars text-xl"></i>
            </button>
            <div class="font-allison text-4xl text-black pt-1 sidebar-restaurant-letter">{{ isset($activeRestaurant) ? substr($activeRestaurant->name, 0, 1) : (substr($siteSettings['restaurant_name'] ?? 'M', 0, 1)) }}</div>
        </header>

        <header
            class="hidden md:flex h-20 w-full bg-white border-b border-gray-100 px-8 items-center justify-between shrink-0 z-10">
            <div class="flex items-center gap-4">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Yönetim Paneli</span>
            </div>

            @if(!request()->routeIs('admin.settings') && !request()->routeIs('admin.tables'))
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" target="_blank"
                    class="bg-amber-50 hover:bg-amber-100 text-[#8C6C47] font-semibold text-xs px-4 py-2.5 rounded-xl border border-amber-100 transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-eye text-xs"></i>
                    <span>Menüyü Görüntüle</span>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="w-10 h-10 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl border border-red-100 transition-colors flex items-center justify-center"
                        title="Çıkış Yap">
                        <i class="fa-solid fa-right-from-bracket text-sm"></i>
                    </button>
                </form>
            </div>
            @endif
        </header>

        <main class="flex-1 w-full overflow-y-auto p-4 md:p-8 no-scrollbar relative">
            @yield('content')
        </main>
    </div>

    @include('admin.partials.mobile-menu')

    <script>
        (function() {
            let audioCtx = null;
            function unlock() {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume().then(() => {
                        let osc = audioCtx.createOscillator();
                        let gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        gain.gain.setValueAtTime(0, audioCtx.currentTime);
                        osc.start(audioCtx.currentTime);
                        osc.stop(audioCtx.currentTime + 0.01);
                    });
                }
                window.removeEventListener('click', unlock);
                window.removeEventListener('keydown', unlock);
                window.removeEventListener('touchstart', unlock);
            }
            window.addEventListener('click', unlock);
            window.addEventListener('keydown', unlock);
            window.addEventListener('touchstart', unlock);

            function playSound() {
                if (!audioCtx) {
                    audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                }
                audioCtx.resume().then(() => {
                    try {
                        let osc1 = audioCtx.createOscillator();
                        let osc2 = audioCtx.createOscillator();
                        let gain1 = audioCtx.createGain();
                        let gain2 = audioCtx.createGain();
                        osc1.connect(gain1);
                        gain1.connect(audioCtx.destination);
                        osc1.type = 'sine';
                        osc1.frequency.setValueAtTime(523.25, audioCtx.currentTime);
                        gain1.gain.setValueAtTime(0, audioCtx.currentTime);
                        gain1.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.05);
                        gain1.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.0);
                        osc2.connect(gain2);
                        gain2.connect(audioCtx.destination);
                        osc2.type = 'sine';
                        osc2.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.2);
                        gain2.gain.setValueAtTime(0, audioCtx.currentTime + 0.2);
                        gain2.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.25);
                        gain2.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 1.0);
                        osc1.start(audioCtx.currentTime);
                        osc1.stop(audioCtx.currentTime + 1.0);
                        osc2.start(audioCtx.currentTime + 0.2);
                        osc2.stop(audioCtx.currentTime + 1.0);
                    } catch(e) {}
                });
            }

            function checkNewOrders() {
                fetch('/admin/api/new-orders-check')
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_new) {
                            playSound();
                        }
                    })
                    .catch(err => {});
            }
            setInterval(checkNewOrders, 5000);
        })();
    </script>
</body>
</html>