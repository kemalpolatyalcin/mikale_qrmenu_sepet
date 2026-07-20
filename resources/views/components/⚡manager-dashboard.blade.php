<?php

use Livewire\Component;
use App\Models\Order;

new class extends Component {
    public $filterStatus = 'all';
    public $searchQuery = '';
    public $selectedOrderId = null;
    public $lastOrderCount = 0;
    public $hasNewOrder = false;

    public function updateStatus($orderId, $newStatus)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = $newStatus;
            $order->save();
        }
    }

    public function deleteOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->delete();
        }
        if ($this->selectedOrderId === $orderId) {
            $this->selectedOrderId = null;
        }
    }

    public function selectOrder($orderId)
    {
        $this->selectedOrderId = $orderId;
    }

    public function closeOrderModal()
    {
        $this->selectedOrderId = null;
    }

    public function dismissNewAlert()
    {
        $this->hasNewOrder = false;
    }

    public function with(): array
    {
        $query = Order::with('items')->latest();

        if ($this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        if (!empty($this->searchQuery)) {
            $q = $this->searchQuery;
            $query->where(function ($sub) use ($q) {
                $sub->where('table_number', 'like', "%{$q}%")
                    ->orWhere('id', 'like', "%{$q}%")
                    ->orWhere('order_note', 'like', "%{$q}%");
            });
        }

        $orders = $query->get();

        $currentCount = Order::count();
        if ($this->lastOrderCount > 0 && $currentCount > $this->lastOrderCount) {
            $this->hasNewOrder = true;
        }
        $this->lastOrderCount = $currentCount;

        $stats = [
            'total_today' => Order::whereDate('created_at', today())->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'revenue_today' => Order::whereDate('created_at', today())->whereNotIn('status', ['cancelled'])->sum('total_amount'),
        ];

        $selectedOrder = $this->selectedOrderId ? Order::with('items')->find($this->selectedOrderId) : null;

        return [
            'orders' => $orders,
            'stats' => $stats,
            'selectedOrder' => $selectedOrder,
        ];
    }
};
?>

<div wire:poll.3s class="min-h-screen bg-slate-900 text-slate-100 font-sans p-6">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        <header class="bg-slate-800/80 backdrop-blur border border-slate-700/60 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-600/20 border border-amber-500/40 rounded-xl flex items-center justify-center text-amber-400 text-2xl shadow-inner">
                    <i class="fa-solid fa-desktop"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                        Masa Sipariş Yönetim Paneli
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Canlı (Real-Time)
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Masa siparişlerini anlık olarak takip edin ve yönetin</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" wire:model.live="searchQuery" placeholder="Masa no veya not ara..."
                        class="w-full bg-slate-950/60 border border-slate-700 rounded-xl py-2 pl-10 pr-4 text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:border-amber-500/60 focus:ring-1 focus:ring-amber-500/60">
                </div>

                <a href="/" target="_blank" class="px-4 py-2 bg-slate-700/60 hover:bg-slate-700 border border-slate-600 rounded-xl text-sm font-semibold text-slate-200 transition-colors flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-store text-amber-400"></i> Menüyü Aç
                </a>
            </div>
        </header>

        @if($hasNewOrder)
            <div class="bg-amber-500/15 border border-amber-500/40 text-amber-300 px-5 py-4 rounded-2xl shadow-lg flex justify-between items-center animate-bounce">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-bell text-xl text-amber-400"></i>
                    <span class="font-bold text-sm">Yeni masa siparişi geldi! Liste otomatik güncellendi.</span>
                </div>
                <button wire:click="dismissNewAlert" class="text-amber-400 hover:text-white transition-colors text-sm font-bold bg-amber-500/20 px-3 py-1 rounded-lg border border-amber-500/30">
                    Anladım
                </button>
            </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bugün Toplam</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['total_today'] }}</span>
                    <i class="fa-solid fa-utensils text-slate-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-amber-500/30 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Bekleyen</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-amber-400">{{ $stats['pending'] }}</span>
                    <i class="fa-solid fa-clock text-amber-500/60 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-blue-500/30 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Hazırlanan</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-blue-400">{{ $stats['preparing'] }}</span>
                    <i class="fa-solid fa-fire text-blue-500/60 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-emerald-500/30 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Servise Hazır</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-emerald-400">{{ $stats['ready'] }}</span>
                    <i class="fa-solid fa-bell-concierge text-emerald-500/60 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-purple-500/30 rounded-2xl p-4 flex flex-col justify-between col-span-2 sm:col-span-1">
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Günlük Ciro</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-extrabold text-purple-300">₺{{ number_format($stats['revenue_today'], 2) }}</span>
                    <i class="fa-solid fa-wallet text-purple-500/60 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 overflow-x-auto pb-1 no-scrollbar border-b border-slate-800">
            <button wire:click="$set('filterStatus', 'all')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'all' ? 'bg-amber-600 text-white shadow-md' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Tüm Siparişler ({{ $orders->count() }})
            </button>
            <button wire:click="$set('filterStatus', 'pending')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'pending' ? 'bg-amber-500 text-slate-950 shadow-md font-bold' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Bekleyenler ({{ $stats['pending'] }})
            </button>
            <button wire:click="$set('filterStatus', 'preparing')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'preparing' ? 'bg-blue-600 text-white shadow-md' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Hazırlananlar ({{ $stats['preparing'] }})
            </button>
            <button wire:click="$set('filterStatus', 'ready')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'ready' ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Hazır ({{ $stats['ready'] }})
            </button>
            <button wire:click="$set('filterStatus', 'completed')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'completed' ? 'bg-slate-700 text-slate-200' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                Tamamlananlar
            </button>
            <button wire:click="$set('filterStatus', 'cancelled')" class="px-4 py-2 rounded-xl font-semibold text-sm transition-all whitespace-nowrap {{ $filterStatus === 'cancelled' ? 'bg-rose-600/80 text-white' : 'bg-slate-800 text-slate-400 hover:text-slate-200' }}">
                İptal Edilenler
            </button>
        </div>

        @if($orders->isEmpty())
            <div class="bg-slate-800/40 border border-slate-700/40 rounded-2xl p-16 text-center text-slate-500">
                <i class="fa-solid fa-clipboard-check text-6xl opacity-30 mb-4"></i>
                <h3 class="text-xl font-semibold text-slate-300 mb-1">Henüz Sipariş Yok</h3>
                <p class="text-sm">Bu filtre altında görüntülenen sipariş bulunmuyor.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach($orders as $order)
                    @php
                        $badgeClass = match($order->status) {
                            'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/40',
                            'preparing' => 'bg-blue-500/20 text-blue-400 border-blue-500/40',
                            'ready' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/40',
                            'completed' => 'bg-slate-700 text-slate-300 border-slate-600',
                            'cancelled' => 'bg-rose-500/20 text-rose-400 border-rose-500/40',
                            default => 'bg-slate-700 text-slate-300 border-slate-600'
                        };
                        $statusLabel = match($order->status) {
                            'pending' => 'Bekliyor',
                            'preparing' => 'Hazırlanıyor',
                            'ready' => 'Servise Hazır',
                            'completed' => 'Tamamlandı',
                            'cancelled' => 'İptal Edildi',
                            default => $order->status
                        };
                    @endphp

                    <div class="bg-slate-800/80 border border-slate-700/80 rounded-2xl p-5 flex flex-col justify-between shadow-lg hover:border-slate-600 transition-all">
                        <div>
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="inline-block bg-amber-500/10 text-amber-400 font-extrabold text-lg px-3 py-1 rounded-xl border border-amber-500/30">
                                        {{ $order->table_number }}
                                    </span>
                                    <span class="block text-[11px] text-slate-400 mt-1">#{{ $order->id }} &bull; {{ $order->created_at->diffForHumans() }}</span>
                                </div>

                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $badgeClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <div class="bg-slate-900/60 rounded-xl p-3 border border-slate-800 space-y-2 mb-4">
                                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Sipariş İçeriği</span>
                                <ul class="space-y-1.5 m-0 p-0 list-none">
                                    @foreach($order->items as $item)
                                        <li class="flex justify-between items-center text-sm">
                                            <span class="text-slate-200 font-medium line-clamp-1">
                                                <span class="text-amber-400 font-bold mr-1.5">{{ $item->quantity }}x</span>
                                                {{ $item->product_name }}
                                            </span>
                                            <span class="text-slate-400 text-xs font-semibold">₺{{ number_format($item->price * $item->quantity, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if($order->order_note)
                                <div class="bg-amber-500/10 border border-amber-500/20 text-amber-300 p-2.5 rounded-xl text-xs mb-3 flex items-start gap-2">
                                    <i class="fa-solid fa-message mt-0.5 text-amber-400"></i>
                                    <span>{{ $order->order_note }}</span>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-2 mb-4 text-xs font-semibold">
                                <span class="bg-slate-700/50 text-slate-300 px-2.5 py-1 rounded-lg border border-slate-600/50 flex items-center gap-1.5">
                                    <i class="fa-solid fa-credit-card text-amber-400"></i>
                                    {{ $order->payment_method === 'card' ? 'Kredi Kartı' : 'Nakit' }}
                                </span>

                                @if($order->cutlery_requested)
                                    <span class="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2.5 py-1 rounded-lg flex items-center gap-1.5">
                                        <i class="fa-solid fa-utensils"></i> Çatal/Peçete İsteniyor
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center pt-3 border-t border-slate-700/60 mb-4">
                                <span class="text-xs text-slate-400 font-medium">Toplam Tutar:</span>
                                <span class="text-xl font-bold text-amber-400">₺{{ number_format($order->total_amount, 2) }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                @if($order->status === 'pending')
                                    <button wire:click="updateStatus({{ $order->id }}, 'preparing')" class="col-span-2 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs transition-colors shadow-md flex justify-center items-center gap-1.5">
                                        <i class="fa-solid fa-fire"></i> Hazırlanıyor Yap
                                    </button>
                                @elseif($order->status === 'preparing')
                                    <button wire:click="updateStatus({{ $order->id }}, 'ready')" class="col-span-2 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs transition-colors shadow-md flex justify-center items-center gap-1.5">
                                        <i class="fa-solid fa-bell-concierge"></i> Servise Hazır Yap
                                    </button>
                                @elseif($order->status === 'ready')
                                    <button wire:click="updateStatus({{ $order->id }}, 'completed')" class="col-span-2 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold text-xs transition-colors shadow-md flex justify-center items-center gap-1.5">
                                        <i class="fa-solid fa-check-double"></i> Tamamla
                                    </button>
                                @else
                                    <button wire:click="selectOrder({{ $order->id }})" class="col-span-2 py-2 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-xl font-semibold text-xs transition-colors">
                                        Detay Gör
                                    </button>
                                @endif

                                @if(in_array($order->status, ['pending', 'preparing']))
                                    <button wire:click="updateStatus({{ $order->id }}, 'cancelled')" class="py-2 bg-rose-600/20 hover:bg-rose-600/40 text-rose-400 border border-rose-500/30 rounded-xl font-semibold text-xs transition-colors">
                                        İptal Et
                                    </button>
                                    <button wire:click="selectOrder({{ $order->id }})" class="py-2 bg-slate-700/60 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-xs transition-colors">
                                        Detay
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($selectedOrder)
            <div class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-800 border border-slate-700 text-slate-100 max-w-lg w-full rounded-2xl shadow-2xl p-6 relative">
                    <button wire:click="closeOrderModal" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    <h2 class="text-xl font-bold text-white mb-1">Masa Sipariş Detayı</h2>
                    <p class="text-xs text-slate-400 mb-4">Sipariş ID #{{ $selectedOrder->id }} &bull; {{ $selectedOrder->created_at->format('d.m.Y H:i:s') }}</p>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center bg-slate-900/60 p-3 rounded-xl border border-slate-700/60">
                            <span class="text-sm font-bold text-amber-400">{{ $selectedOrder->table_number }}</span>
                            <span class="text-xs font-semibold px-3 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                {{ strtoupper($selectedOrder->status) }}
                            </span>
                        </div>

                        <div class="bg-slate-900/40 p-4 rounded-xl border border-slate-700/40">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Ürünler</span>
                            <ul class="space-y-2 m-0 p-0 list-none">
                                @foreach($selectedOrder->items as $item)
                                    <li class="flex justify-between text-sm">
                                        <span>{{ $item->quantity }}x {{ $item->product_name }}</span>
                                        <span class="font-bold">₺{{ number_format($item->price * $item->quantity, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="text-xs space-y-1 text-slate-300">
                            <div><strong>Ödeme Yöntemi:</strong> {{ $selectedOrder->payment_method === 'card' ? 'Kredi Kartı' : 'Nakit' }}</div>
                            <div><strong>Çatal / Peçete:</strong> {{ $selectedOrder->cutlery_requested ? 'Evet' : 'Hayır' }}</div>
                            @if($selectedOrder->coupon_code)
                                <div><strong>Kupon Kodu:</strong> {{ $selectedOrder->coupon_code }}</div>
                            @endif
                            @if($selectedOrder->order_note)
                                <div class="mt-2 p-2 bg-amber-500/10 text-amber-300 rounded-lg"><strong>Sipariş Notu:</strong> {{ $selectedOrder->order_note }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="deleteOrder({{ $selectedOrder->id }})" class="w-1/2 py-2.5 bg-rose-600 hover:bg-rose-500 text-white rounded-xl font-bold text-xs transition-colors">
                            Siparişi Sil
                        </button>
                        <button wire:click="closeOrderModal" class="w-1/2 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold text-xs transition-colors">
                            Kapat
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
