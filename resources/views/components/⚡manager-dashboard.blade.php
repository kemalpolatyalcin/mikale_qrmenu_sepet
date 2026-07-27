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
            $tablesData[] = [
                'id' => $table->id,
                'name' => $table->name,
                'token' => $table->token,
                'total_amount' => $totalAmount,
                'has_active' => $totalAmount > 0,
                'orders' => $tableOrders
            ];
        }

        if ($this->searchQuery !== '') {
            $q = $this->searchQuery;
            $tablesData = array_filter($tablesData, function ($t) use ($q) {
                return str_contains(strtolower($t['name']), strtolower($q)) || str_contains(strtolower($t['token']), strtolower($q));
            });
        }

        $currentCount = Order::where('restaurant_id', $restaurantId)->count();
        if ($this->lastOrderCount > 0 && $currentCount > $this->lastOrderCount) {
            $this->hasNewOrder = true;
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

<div wire:poll.5s class="min-h-screen bg-slate-900 text-slate-100 font-sans p-6">
    <div class="max-w-[1600px] mx-auto space-y-6">
        
        <header class="bg-slate-800/80 backdrop-blur border border-slate-700/60 rounded-2xl p-5 shadow-xl flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-600/20 border border-amber-500/40 rounded-xl flex items-center justify-center text-amber-400 text-2xl shadow-inner">
                    <i class="fa-solid fa-table"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                        Masa Görünümü
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 animate-pulse">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Canlı
                        </span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-0.5">Masaların durumlarını ve aktif adisyonlarını takip edin</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" wire:model.live="searchQuery" placeholder="Masa ara..."
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
                    <span class="font-bold text-sm">Yeni sipariş geldi! Masa durumları güncellendi.</span>
                </div>
                <button wire:click="dismissNewAlert" class="text-amber-400 hover:text-white transition-colors text-sm font-bold bg-amber-500/20 px-3 py-1 rounded-lg border border-amber-500/30">
                    Anladım
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-slate-800/60 border border-slate-700/60 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bugün Toplam Sipariş</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['total_today'] }}</span>
                    <i class="fa-solid fa-utensils text-slate-500 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-amber-500/30 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Bekleyen Aktif Siparişler</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-3xl font-extrabold text-amber-400">{{ $stats['pending'] }}</span>
                    <i class="fa-solid fa-clock text-amber-500/60 text-xl"></i>
                </div>
            </div>

            <div class="bg-slate-800/60 border border-purple-500/30 rounded-2xl p-4 flex flex-col justify-between">
                <span class="text-xs font-semibold text-purple-400 uppercase tracking-wider">Bugün Toplam Tahsilat</span>
                <div class="flex items-baseline justify-between mt-2">
                    <span class="text-2xl font-extrabold text-purple-300">₺{{ number_format($stats['revenue_today'], 2) }}</span>
                    <i class="fa-solid fa-wallet text-purple-500/60 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($tables as $t)
                <button wire:click="selectTable('{{ $t['name'] }}')" class="flex flex-col justify-between p-5 rounded-2xl border text-left transition-all duration-200 outline-none
                    {{ $t['has_active'] ? 'bg-amber-600/10 border-amber-500/50 hover:bg-amber-600/20' : 'bg-slate-800/40 border-slate-700/60 hover:bg-slate-800/60' }}
                ">
                    <div class="flex justify-between items-start w-full">
                        <span class="text-lg font-bold text-white tracking-wide">{{ $t['name'] }}</span>
                        @if($t['has_active'])
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></span>
                        @else
                            <span class="text-[9px] uppercase font-bold text-slate-500 tracking-wider">Boş</span>
                        @endif
                    </div>

                    <div class="mt-8">
                        @if($t['has_active'])
                            <span class="text-xs text-amber-400 font-semibold block mb-0.5">Aktif Hesap:</span>
                            <span class="text-xl font-black text-white">₺{{ number_format($t['total_amount'], 2) }}</span>
                        @else
                            <span class="text-xs text-slate-500 block mb-0.5">Hesap Yok</span>
                            <span class="text-lg font-extrabold text-slate-600">₺0.00</span>
                        @endif
                    </div>
                </button>
            @empty
                <div class="col-span-full bg-slate-800/20 border border-slate-700/40 rounded-2xl p-16 text-center text-slate-500">
                    <i class="fa-solid fa-chair text-6xl opacity-30 mb-4"></i>
                    <h3 class="text-xl font-semibold text-slate-300 mb-1">Masa Bulunamadı</h3>
                    <p class="text-sm">Restoranınıza ait kayıtlı masa bulunmuyor.</p>
                </div>
            @endforelse
        </div>

        @if($selectedTable)
            <div class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
                <div class="bg-slate-800 border border-slate-700 text-slate-100 max-w-lg w-full rounded-2xl shadow-2xl p-6 relative">
                    <button wire:click="closeTableModal" class="absolute top-4 right-4 text-slate-400 hover:text-white text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>

                    @if(!$showSettlementForm)
                        <h2 class="text-xl font-bold text-white mb-1">Masa Detayı: {{ $selectedTable['name'] }}</h2>
                        <p class="text-xs text-slate-400 mb-4">Masa adisyonu detayları ve hesap tahsilatı</p>

                        <div class="space-y-4 mb-6">
                            @if(count($selectedTable['items']) > 0)
                                <div class="bg-slate-900/60 p-4 rounded-xl border border-slate-700/60">
                                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Ürünler</span>
                                    <ul class="space-y-2.5 m-0 p-0 list-none max-h-48 overflow-y-auto no-scrollbar">
                                        @foreach($selectedTable['items'] as $item)
                                            <li class="flex justify-between text-sm">
                                                <span><span class="text-amber-400 font-bold mr-1.5">{{ $item['quantity'] }}x</span>{{ $item['product_name'] }}</span>
                                                <span class="font-bold">₺{{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div class="flex justify-between items-center bg-slate-950/60 px-4 py-3 rounded-xl border border-slate-700/40">
                                    <span class="text-sm font-semibold text-slate-400">Toplam Hesap:</span>
                                    <span class="text-xl font-black text-amber-400">₺{{ number_format($selectedTable['total_amount'], 2) }}</span>
                                </div>
                            @else
                                <div class="bg-slate-900/30 p-8 rounded-xl border border-slate-700/20 text-center text-slate-500">
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
                                <button wire:click="resetTableSession('{{ $selectedTable['name'] }}')" class="w-full py-2.5 bg-rose-600/10 hover:bg-rose-600 text-rose-400 hover:text-white rounded-xl font-bold text-xs transition-colors border border-rose-500/20">
                                    Masayı Sıfırla (İptal Et)
                                </button>
                            @endif
                            <button wire:click="closeTableModal" class="w-full py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold text-xs transition-colors">
                                Kapat
                            </button>
                        </div>
                    @else
                        <h2 class="text-xl font-bold text-white mb-1">Hesap Tahsilat: {{ $selectedTable['name'] }}</h2>
                        <p class="text-xs text-slate-400 mb-4">Ödeme yöntemini seçerek hesabı kapatın</p>

                        <div class="space-y-4 mb-6">
                            <div class="flex justify-between items-center bg-slate-950/60 px-4 py-3 rounded-xl border border-slate-700/40">
                                <span class="text-sm font-semibold text-slate-400">Tahsil Edilecek Tutar:</span>
                                <span class="text-xl font-black text-amber-400">₺{{ number_format($selectedTable['total_amount'], 2) }}</span>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Ödeme Yöntemi</label>
                                <div class="grid grid-cols-2 gap-2">
                                    @foreach(['Nakit', 'Kredi Kartı', 'Cari Hesap', 'Yemek Fişi', 'Online Ödeme', 'Ödenmez'] as $method)
                                        <button wire:click="$set('selectedPaymentMethod', '{{ $method }}')" class="py-3 px-4 rounded-xl text-xs font-bold border transition-all text-center
                                            {{ $selectedPaymentMethod === $method ? 'bg-amber-600 border-amber-500 text-white shadow-md' : 'bg-slate-900 border-slate-700 hover:bg-slate-800 text-slate-300' }}
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
                            <button wire:click="cancelSettlement" class="w-1/2 py-2.5 bg-slate-700 hover:bg-slate-600 text-white rounded-xl font-bold text-xs transition-colors">
                                Geri Dön
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

    </div>
</div>
