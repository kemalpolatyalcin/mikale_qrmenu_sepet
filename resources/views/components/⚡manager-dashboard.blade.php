<?php

use Livewire\Component;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\Transaction;
use Illuminate\Support\Str;

new class extends Component {
    public $searchQuery = '';
    public $selectedTableName = null;
    public $showSettlementForm = false;
    public $selectedPaymentMethod = 'Nakit';
    public $lastOrderCount = 0;
    public $hasNewOrder = false;
    public $newOrderTableNumber = null;
    public $newOrderDescription = null;

    public function selectTable($name)
    {
        $this->selectedTableName = $name;
        $this->showSettlementForm = false;
        $this->selectedPaymentMethod = 'Nakit';
    }

    public function closeTableModal()
    {
        $this->selectedTableName = null;
        $this->showSettlementForm = false;
    }

    public function startSettlement()
    {
        $this->showSettlementForm = true;
    }

    public function cancelSettlement()
    {
        $this->showSettlementForm = false;
    }

    public function settleBill()
    {
        $restaurantId = session('active_restaurant_id');
        if (!$restaurantId) {
            $first = Restaurant::first();
            $restaurantId = $first ? $first->id : null;
        }

        $activeOrders = Order::with('items')
            ->where('table_number', $this->selectedTableName)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->get();

        if ($activeOrders->isEmpty()) {
            $this->closeTableModal();
            return;
        }

        $totalAmount = $activeOrders->sum('total_amount');
        $itemsList = [];

        foreach ($activeOrders as $order) {
            foreach ($order->items as $item) {
                $itemsList[] = $item->quantity . 'x ' . $item->product_name;
            }
            $order->status = 'completed';
            $order->save();
        }

        $table = Table::where('name', $this->selectedTableName)->where('restaurant_id', $restaurantId)->first();
        if ($table) {
            $table->active_session_id = Str::random(40);
            $table->session_token = Str::random(32);
            $table->session_expires_at = now()->addHours(2);
            $table->save();
        }

        Transaction::create([
            'restaurant_id' => $restaurantId,
            'table_name' => $this->selectedTableName,
            'amount' => $totalAmount,
            'payment_method' => $this->selectedPaymentMethod,
            'details' => implode(', ', $itemsList)
        ]);

        $this->closeTableModal();
        $this->dispatch('bill-settled');
    }

    public function resetTableSession($tableName)
    {
        $restaurantId = session('active_restaurant_id');
        if (!$restaurantId) {
            $first = Restaurant::first();
            $restaurantId = $first ? $first->id : null;
        }

        $table = Table::where('name', $tableName)->where('restaurant_id', $restaurantId)->first();
        if ($table) {
            $table->active_session_id = Str::random(40);
            $table->session_token = Str::random(32);
            $table->session_expires_at = now()->addHours(2);
            $table->save();
        }

        Order::where('table_number', $tableName)
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $this->closeTableModal();
        $this->dispatch('table-reset');
    }

    public function dismissNewAlert()
    {
        $this->hasNewOrder = false;
    }

    public function with(): array
    {
        $restaurantId = session('active_restaurant_id');
        if (!$restaurantId) {
            $first = Restaurant::first();
            $restaurantId = $first ? $first->id : null;
        }

        $allTables = Table::where('restaurant_id', $restaurantId)->orderBy('name', 'asc')->get();
        $activeOrders = Order::with('items')
            ->where('restaurant_id', $restaurantId)
            ->where('status', 'pending')
            ->get();

        $tablesData = [];
        foreach ($allTables as $table) {
            $tableOrders = $activeOrders->where('table_number', $table->name);
            $totalAmount = $tableOrders->sum('total_amount');
            $hasWaiterCall = false;
            foreach ($tableOrders as $o) {
                if (str_contains(mb_strtolower($o->order_note ?? '', 'UTF-8'), 'garson')) {
                    $hasWaiterCall = true;
                    break;
                }
            }
            $tablesData[] = [
                'id' => $table->id,
                'name' => $table->name,
                'token' => $table->token,
                'total_amount' => $totalAmount,
                'has_active' => $totalAmount > 0 || $hasWaiterCall,
                'has_waiter_call' => $hasWaiterCall,
                'orders' => $tableOrders
            ];
        }

        if ($this->searchQuery !== '') {
            $q = mb_strtolower($this->searchQuery, 'UTF-8');
            $tablesData = array_filter($tablesData, function ($t) use ($q) {
                $name = mb_strtolower($t['name'], 'UTF-8');
                $token = mb_strtolower($t['token'] ?? '', 'UTF-8');
                return str_contains($name, $q) || str_contains($token, $q);
            });
        }

        $currentCount = Order::where('restaurant_id', $restaurantId)->count();
        if ($this->lastOrderCount > 0 && $currentCount > $this->lastOrderCount) {
            $this->hasNewOrder = true;
            $latest = Order::with('items')->where('restaurant_id', $restaurantId)->latest('id')->first();
            if ($latest) {
                $this->newOrderTableNumber = $latest->table_number;
                $this->newOrderDescription = $latest->items->map(fn($item) => $item->quantity . 'x ' . $item->product_name)->join(', ');
                $this->dispatch('new-order-received');
            } else {
                $this->newOrderTableNumber = null;
                $this->newOrderDescription = null;
                $this->dispatch('new-order-received');
            }
        }
        $this->lastOrderCount = $currentCount;

        $stats = [
            'total_today' => Order::where('restaurant_id', $restaurantId)->whereDate('created_at', today())->count(),
            'pending' => Order::where('restaurant_id', $restaurantId)->where('status', 'pending')->count(),
            'revenue_today' => Transaction::where('restaurant_id', $restaurantId)->whereDate('created_at', today())->sum('amount'),
        ];

        $selectedTableDetails = null;
        if ($this->selectedTableName) {
            $selectedTableOrders = $activeOrders->where('table_number', $this->selectedTableName);
            $totalAmount = $selectedTableOrders->sum('total_amount');
            
            $itemsList = [];
            foreach ($selectedTableOrders as $order) {
                foreach ($order->items as $item) {
                    $key = $item->product_name;
                    if (isset($itemsList[$key])) {
                        $itemsList[$key]['quantity'] += $item->quantity;
                    } else {
                        $itemsList[$key] = [
                            'product_name' => $item->product_name,
                            'price' => $item->price,
                            'quantity' => $item->quantity
                        ];
                    }
                }
            }

            $selectedTableDetails = [
                'name' => $this->selectedTableName,
                'total_amount' => $totalAmount,
                'items' => array_values($itemsList),
                'orders_count' => $selectedTableOrders->count()
            ];
        }

        return [
            'tables' => $tablesData,
            'stats' => $stats,
            'selectedTable' => $selectedTableDetails,
        ];
    }
};
?>

<div wire:poll.2s class="min-h-screen bg-brand-bg text-gray-800 font-sans p-6">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        <header class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#8C6C47]/10 border border-[#8C6C47]/20 rounded-xl flex items-center justify-center text-[#8C6C47] text-2xl shadow-inner">
                    <i class="fa-solid fa-table"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-800 flex items-center gap-3">
                        Masa Görünümü
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 border border-emerald-500/30 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Canlı
                        </span>
                    </h1>
                    <p class="text-xs text-gray-500 mt-0.5">Masaların durumlarını ve aktif adisyonlarını takip edin</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input type="text" wire:model.live="searchQuery" placeholder="Masa ara..."
                        class="w-full bg-white border border-gray-200 rounded-xl py-2 pl-10 pr-4 text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#8C6C47] focus:ring-1 focus:ring-[#8C6C47]">
                </div>

                <a href="/" target="_blank" class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition-colors flex items-center gap-2 whitespace-nowrap shadow-sm">
                    <i class="fa-solid fa-store text-[#8C6C47]"></i> Menüyü Aç
                </a>

                <button type="button" id="refresh-connection-btn" wire:click="$refresh" onclick="refreshDesktopConnection()"
                    class="px-4 py-2 bg-[#8C6C47] hover:bg-[#735738] border border-[#8C6C47]/20 rounded-xl text-sm font-semibold text-white transition-colors flex items-center gap-2 whitespace-nowrap shadow-sm">
                    <i class="fa-solid fa-rotate"></i> Bağlantıyı Yenile
                </button>


            </div>
        </header>

        @if($hasNewOrder)
            <style>
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes swing {
                    0%, 100% { transform: rotate(0deg); }
                    20% { transform: rotate(15deg); }
                    40% { transform: rotate(-10deg); }
                    60% { transform: rotate(5deg); }
                    80% { transform: rotate(-5deg); }
                }
                .animate-slide-in-right {
                    animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
                }
                .animate-swing {
                    animation: swing 1s ease-in-out infinite;
                }
            </style>
            <div class="fixed top-6 right-6 z-[100] max-w-sm w-full bg-white border-l-4 border-amber-500 shadow-2xl rounded-2xl p-4 flex justify-between items-start gap-4 transition-all duration-300 animate-slide-in-right">
                <div class="flex gap-3">
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 shrink-0">
                        <i class="fa-solid fa-bell text-lg animate-swing"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">
                            @if($newOrderTableNumber)
                                Masa {{ $newOrderTableNumber }} - Yeni Sipariş!
                            @else
                                Yeni Sipariş Alındı!
                            @endif
                        </h4>
                        <p class="text-xs text-gray-500 mt-1">
                            @if($newOrderDescription)
                                {{ $newOrderDescription }}
                            @else
                                Masa durumları ve sipariş listesi güncellendi.
                            @endif
                        </p>
                    </div>
                </div>
                <button wire:click="dismissNewAlert" class="text-gray-400 hover:text-gray-600 transition-colors text-sm font-bold bg-gray-50 hover:bg-gray-100 p-1.5 rounded-lg border border-gray-200">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white border border-gray-200 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Bugün Toplam Sipariş</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-gray-800">{{ $stats['total_today'] }}</span>
                    <i class="fa-solid fa-utensils text-gray-400 text-xl"></i>
                </div>
            </div>

            <div class="bg-white border border-[#8C6C47]/30 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                <span class="text-xs font-semibold text-[#8C6C47] uppercase tracking-wider">Bekleyen Aktif Siparişler</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-[#8C6C47]">{{ $stats['pending'] }}</span>
                    <i class="fa-solid fa-clock text-[#8C6C47]/60 text-xl"></i>
                </div>
            </div>

            <div class="bg-white border border-purple-500/20 rounded-2xl p-4 flex flex-col justify-between shadow-sm">
                <span class="text-xs font-semibold text-purple-600 uppercase tracking-wider">Bugün Toplam Tahsilat</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-extrabold text-purple-800">₺{{ number_format($stats['revenue_today'], 2) }}</span>
                    <i class="fa-solid fa-wallet text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($tables as $t)
                <button wire:click="selectTable('{{ $t['name'] }}')" class="flex flex-col justify-between p-5 rounded-2xl border text-left transition-all duration-200 outline-none
                    {{ $t['has_waiter_call'] ? 'bg-red-50/50 border-red-200 hover:bg-red-50 shadow-sm' : ($t['has_active'] ? 'bg-[#8C6C47]/10 border-[#8C6C47]/40 hover:bg-[#8C6C47]/20 shadow-sm' : 'bg-white border-gray-200 hover:border-gray-300 hover:bg-gray-50/50 shadow-sm') }}
                ">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-lg font-bold text-gray-800 tracking-wide">{{ $t['name'] }}</span>
                        @if($t['has_waiter_call'])
                            <span class="flex items-center gap-1 bg-red-500 text-white px-2 py-0.5 rounded-full text-[9px] font-extrabold border border-red-600/10 animate-pulse">
                                <i class="fa-solid fa-bell-concierge"></i> Çağrı
                            </span>
                        @elseif($t['has_active'])
                            <span class="w-2.5 h-2.5 rounded-full bg-[#8C6C47] animate-pulse"></span>
                        @else
                            <span class="text-[9px] uppercase font-bold text-gray-400 tracking-wider">Boş</span>
                        @endif
                    </div>

                    <div class="mt-8">
                        @if($t['has_waiter_call'])
                            <span class="text-xs text-red-600 font-semibold block mb-0.5">Garson Çağrısı:</span>
                            <span class="text-sm font-bold text-gray-900 truncate block">
                                @php
                                    $wOrder = $t['orders']->first();
                                    $noteVal = $wOrder ? $wOrder->order_note : '';
                                    if (strpos($noteVal, '(') !== false && strpos($noteVal, ')') !== false) {
                                        $start = strpos($noteVal, '(') + 1;
                                        $len = strpos($noteVal, ')') - $start;
                                        echo substr($noteVal, $start, $len);
                                    } else {
                                        echo 'Genel Çağrı';
                                    }
                                @endphp
                            </span>
                        @elseif($t['has_active'])
                            <span class="text-xs text-[#8C6C47] font-semibold block mb-0.5">Aktif Hesap:</span>
                            <span class="text-xl font-black text-gray-950">₺{{ number_format($t['total_amount'], 2) }}</span>
                        @else
                            <span class="text-xs text-gray-400 block mb-0.5">Hesap Yok</span>
                            <span class="text-lg font-extrabold text-gray-400">₺0.00</span>
                        @endif
                    </div>
                </button>
            @empty
                <div class="col-span-full bg-white border border-gray-200 rounded-2xl p-16 text-center text-gray-500 shadow-sm">
                    <i class="fa-solid fa-chair text-6xl opacity-30 mb-4 text-gray-400"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-1">Masa Bulunamadı</h3>
                    <p class="text-sm">Restoranınıza ait kayıtlı masa bulunmuyor.</p>
                </div>
            @endforelse
        </div>

        @if($selectedTable)
            <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-white border border-gray-200 text-gray-800 max-w-lg w-full rounded-2xl shadow-2xl p-6 relative">
                    <button wire:click="closeTableModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    @if(!$showSettlementForm)
                        <h2 class="text-xl font-bold text-gray-800 mb-1">Masa Detayı: {{ $selectedTable['name'] }}</h2>
                        <p class="text-xs text-gray-500 mb-4">Masa adisyonu detayları ve hesap tahsilatı</p>

                        <div class="space-y-4 mb-6">
                            @if(count($selectedTable['items']) > 0)
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Ürünler</span>
                                    <ul class="space-y-2.5 m-0 p-0 list-none max-h-48 overflow-y-auto no-scrollbar">
                                        @foreach($selectedTable['items'] as $item)
                                            <li class="flex justify-between text-sm">
                                                <span><span class="text-[#8C6C47] font-bold mr-1.5">{{ $item['quantity'] }}x</span>{{ $item['product_name'] }}</span>
                                                <span class="font-bold text-gray-800">₺{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="flex justify-between items-center bg-gray-100 px-4 py-3 rounded-xl border border-gray-200">
                                    <span class="text-sm font-semibold text-gray-500">Toplam Hesap:</span>
                                    <span class="text-xl font-black text-[#8C6C47]">₺{{ number_format($selectedTable['total_amount'], 2) }}</span>
                                </div>
                            @else
                                <div class="bg-gray-50 p-8 rounded-xl border border-gray-200 text-center text-gray-400">
                                    <i class="fa-solid fa-circle-info text-2xl mb-2 opacity-50"></i>
                                    <p class="text-sm">Bu masaya ait aktif adisyon kaydı bulunmuyor.</p>
                                </div>
                            @endif
                        </div>

                        <div class="flex flex-col gap-2">
                            @if($selectedTable['total_amount'] > 0)
                                <button wire:click="startSettlement" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition-colors shadow-md flex justify-center items-center gap-1.5">
                                    <i class="fa-solid fa-cash-register"></i> Hesap Tahsilat
                                </button>
                                <button wire:click="resetTableSession('{{ $selectedTable['name'] }}')" class="w-full py-2.5 bg-rose-50 hover:bg-rose-600 text-rose-600 hover:text-white rounded-xl font-bold text-xs transition-colors border border-rose-100">
                                    Masayı Sıfırla (İptal Et)
                                </button>
                            @endif
                            <button wire:click="closeTableModal" class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition-colors">
                                Kapat
                            </button>
                        </div>
                    @else
                        <h2 class="text-xl font-bold text-gray-800 mb-1">Hesap Tahsilat: {{ $selectedTable['name'] }}</h2>
                        <p class="text-xs text-gray-500 mb-4">Ödeme yöntemini seçerek hesabı kapatın</p>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center bg-gray-100 px-4 py-3 rounded-xl border border-gray-200">
                                <span class="text-sm font-semibold text-gray-500">Tahsil Edilecek Tutar:</span>
                                <span class="text-xl font-black text-[#8C6C47]">₺{{ number_format($selectedTable['total_amount'], 2) }}</span>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Ödeme Yöntemi</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach(['Nakit', 'Kredi Kartı', 'Cari Hesap', 'Yemek Fişi', 'Online Ödeme', 'Ödenmez'] as $method)
                                        <button wire:click="$set('selectedPaymentMethod', '{{ $method }}')" class="py-3 px-4 rounded-xl text-xs font-bold border transition-all text-center
                                            {{ $selectedPaymentMethod === $method ? 'bg-[#8C6C47] border-[#8C6C47] text-white shadow-md' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 text-gray-600' }}
                                        ">
                                            {{ $method }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button wire:click="settleBill" class="w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition-colors shadow-md">
                                Hesabı Kapat
                            </button>
                            <button wire:click="cancelSettlement" class="w-1/2 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold text-xs transition-colors">
                                Geri Dön
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    function refreshDesktopConnection() {
        const btn = document.getElementById('refresh-connection-btn');
        if (!btn) return;
        const icon = btn.querySelector('i');
        if (icon) icon.classList.add('fa-spin');
        btn.disabled = true;

        const token = localStorage.getItem('admin_token');

        fetch('/api/sync/status', {
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (icon) icon.classList.remove('fa-spin');
            btn.disabled = false;
            if (data.status === 'success') {
                alert('Masaüstü bağlantısı başarıyla yenilendi.');
            } else {
                alert('Masaüstü bağlantısı yenilenemedi.');
            }
        })
        .catch(() => {
            if (icon) icon.classList.remove('fa-spin');
            btn.disabled = false;
            alert('Masaüstü bağlantısı yenilenirken bir hata oluştu.');
        });
    }

    window.addEventListener('new-order-received', () => {
        playNotificationChime();
    });

    function playNotificationChime() {
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
</script>
