<?php

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Order;
use App\Models\OrderItem;

new class extends Component {
    #[Url(as: 'masa')]
    public $tableNumber = 'Bilinmeyen Masa';

    public $wantsCutlery = false;
    public $paymentMethod = 'cash';
    public $couponCode = '';
    public $orderNote = '';
    public $discount = 0;
    public $prepTime = 25;

    #[On('cartUpdated')]
    public function refreshCart()
    {
    }

    #[On('add-to-cart')]
    public function addToCart($id, $name, $price)
    {
        Cart::add([
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'attributes' => []
        ]);
        $this->dispatch('cartUpdated');
        $this->dispatch('open-cart');
    }

    public function addDrink($id, $name, $price)
    {
        Cart::add([
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => 1,
            'attributes' => []
        ]);
        $this->dispatch('cartUpdated');
    }

    public function updateQuantity($id, $action)
    {
        $item = Cart::get($id);
        if (!$item) {
            return;
        }
        if ($action === 'increase') {
            Cart::update($id, ['quantity' => 1]);
        } elseif ($action === 'decrease') {
            if ($item->quantity <= 1) {
                Cart::remove($id);
            } else {
                Cart::update($id, ['quantity' => -1]);
            }
        }
        $this->dispatch('cartUpdated');
    }

    public function removeItem($rowId)
    {
        Cart::remove($rowId);
        $this->dispatch('cartUpdated');
    }

    public function clearCart()
    {
        Cart::clear();
        $this->discount = 0;
        $this->couponCode = '';
        $this->orderNote = '';
        $this->dispatch('cartUpdated');
    }

    public function applyCoupon()
    {
        if (strtoupper($this->couponCode) === 'INDIRIM10') {
            $this->discount = 10;
        } else {
            $this->discount = 0;
        }
    }

    public function checkout()
    {
        if (Cart::isEmpty()) {
            return;
        }

        $finalTotal = Cart::getTotal() - $this->discount;
        if ($finalTotal < 0) {
            $finalTotal = 0;
        }

        $order = Order::create([
            'table_number' => $this->tableNumber,
            'total_amount' => $finalTotal,
            'cutlery_requested' => $this->wantsCutlery,
            'payment_method' => $this->paymentMethod,
            'coupon_code' => $this->discount > 0 ? strtoupper($this->couponCode) : null,
            'order_note' => $this->orderNote,
            'status' => 'pending',
        ]);

        foreach (Cart::getContent() as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->id,
                'product_name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
            ]);
        }

        Cart::clear();
        $this->wantsCutlery = false;
        $this->paymentMethod = 'cash';
        $this->couponCode = '';
        $this->orderNote = '';
        $this->discount = 0;

        $this->dispatch('cartUpdated');
        $this->dispatch('close-cart');
        $this->dispatch('order-success');
    }

    public function with(): array
    {
        return [
            'cartItems' => Cart::getContent()->sortBy('id'),
            'cartTotal' => Cart::getTotal(),
        ];
    }
};
?>

<div>
    <div x-data="{ open: false, showSuccess: false }" @open-cart.window="open = true" @close-cart.window="open = false"
        @order-success.window="showSuccess = true; setTimeout(() => { showSuccess = false }, 4000)">

        <div x-show="showSuccess" style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-[-20px]" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-[-20px]"
            class="fixed top-5 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-xl shadow-2xl z-[200] flex items-center gap-3 font-semibold min-w-[300px] justify-center">
            <i class="fa-solid fa-circle-check text-xl"></i>
            <span>Siparişiniz başarıyla mutfağa iletildi!</span>
        </div>

        <div x-show="open" style="display: none;" x-transition.opacity.duration.300ms @click="open = false"
            class="fixed inset-0 bg-black/50 z-[90] backdrop-blur-sm cursor-pointer">
        </div>

        <div :class="open ? 'translate-x-0' : 'translate-x-full'"
            class="fixed top-0 right-0 h-full w-full sm:w-[450px] bg-brand-bg shadow-2xl z-[100] transform transition-transform duration-300 ease-in-out flex flex-col font-sans">

            <div class="flex flex-col bg-white border-b border-gray-100 shrink-0">
                <div class="flex justify-between items-center px-6 py-5">
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-brand-text m-0">Sepetim</h2>
                        <span class="bg-brand-gold text-white rounded-full px-3 py-1 text-sm font-bold shadow-sm">
                            {{ $cartItems->count() }}
                        </span>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-red-500 transition-colors text-xl">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div
                    class="bg-amber-50 px-6 py-2 border-t border-amber-100 flex justify-between items-center text-amber-700 text-sm font-semibold">
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-clock animate-pulse"></i>
                        <span>Tahmini Süre: {{ $prepTime }} - {{ $prepTime + 10 }} dk</span>
                    </div>
                    <div class="bg-amber-100 px-2 py-1 rounded text-xs font-bold border border-amber-200">
                        {{ $tableNumber }}
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 no-scrollbar relative">
                @if($cartItems->isEmpty())
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-solid fa-basket-shopping text-5xl mb-4 opacity-20"></i>
                        <p class="font-medium text-gray-500">Sepetiniz şu an boş.</p>
                    </div>
                @else
                    <ul class="space-y-4 m-0 p-0 list-none mb-6">
                        @foreach($cartItems as $item)
                            <li class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex flex-col gap-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-brand-text m-0">{{ $item->name }}</h4>
                                        <span class="font-bold text-brand-gold text-sm">{{ $item->price }} TL</span>
                                    </div>
                                    <button wire:click="removeItem('{{ $item->id }}')"
                                        class="text-gray-300 hover:text-red-500 transition-colors">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>

                                <div class="flex justify-between items-center mt-2 pt-3 border-t border-gray-50">
                                    <div class="flex items-center bg-gray-50 border border-gray-200 rounded-lg">
                                        <button wire:click="updateQuantity('{{ $item->id }}', 'decrease')"
                                            class="px-3 py-1 text-gray-500 hover:text-brand-text font-bold transition-colors">-</button>
                                        <span class="px-2 font-semibold text-sm w-8 text-center">{{ $item->quantity }}</span>
                                        <button wire:click="updateQuantity('{{ $item->id }}', 'increase')"
                                            class="px-3 py-1 text-gray-500 hover:text-brand-text font-bold transition-colors">+</button>
                                    </div>
                                    <span class="font-bold text-gray-800">{{ $item->price * $item->quantity }} TL</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-3">Yanında İyi Gider</h3>
                        <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar">
                            <button wire:click="addDrink(101, 'Kutu Kola', 30)"
                                class="min-w-[120px] bg-white border border-gray-200 p-3 rounded-xl flex flex-col items-center hover:border-brand-gold transition-colors shadow-sm">
                                <span class="font-semibold text-sm text-gray-800">Kutu Kola</span>
                                <span class="text-xs font-bold text-brand-gold mt-1">30 TL</span>
                            </button>
                            <button wire:click="addDrink(102, 'Ayran', 20)"
                                class="min-w-[120px] bg-white border border-gray-200 p-3 rounded-xl flex flex-col items-center hover:border-brand-gold transition-colors shadow-sm">
                                <span class="font-semibold text-sm text-gray-800">Ayran</span>
                                <span class="text-xs font-bold text-brand-gold mt-1">20 TL</span>
                            </button>
                            <button wire:click="addDrink(103, 'Su', 10)"
                                class="min-w-[120px] bg-white border border-gray-200 p-3 rounded-xl flex flex-col items-center hover:border-brand-gold transition-colors shadow-sm">
                                <span class="font-semibold text-sm text-gray-800">Su</span>
                                <span class="text-xs font-bold text-brand-gold mt-1">10 TL</span>
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 space-y-4 mb-4">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" wire:model.live="wantsCutlery"
                                class="w-5 h-5 text-brand-gold border-gray-300 rounded focus:ring-brand-gold">
                            <span class="text-sm font-semibold text-gray-700">Çatal, bıçak ve peçete istiyorum</span>
                        </label>

                        <div class="border-t border-gray-100 pt-4">
                            <textarea wire:model="orderNote"
                                placeholder="Sipariş notunuz (Örn: Soğansız olsun, az pişsin...)"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-gold resize-none h-20 bg-gray-50"></textarea>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <span class="text-sm font-bold text-gray-700 block mb-2">Ödeme Yöntemi</span>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="paymentMethod" value="cash" name="payment"
                                        class="w-4 h-4 text-brand-gold focus:ring-brand-gold">
                                    <span class="text-sm text-gray-600 font-medium">Nakit</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" wire:model.live="paymentMethod" value="card" name="payment"
                                        class="w-4 h-4 text-brand-gold focus:ring-brand-gold">
                                    <span class="text-sm text-gray-600 font-medium">Kredi Kartı</span>
                                </label>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4">
                            <div class="flex gap-2">
                                <input type="text" wire:model="couponCode" placeholder="Kupon Kodu"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-brand-gold bg-gray-50">
                                <button wire:click="applyCoupon"
                                    class="bg-gray-800 hover:bg-black text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">Uygula</button>
                            </div>
                            @if($discount > 0)
                                <span class="text-xs font-bold text-green-500 mt-2 block">Kupon uygulandı!</span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            @if(!$cartItems->isEmpty())
                <div class="p-6 bg-white border-t border-gray-100 shrink-0 shadow-[0_-5px_15px_rgba(0,0,0,0.02)]">
                    <div class="flex flex-col gap-2 mb-4">
                        <div class="flex justify-between text-gray-500 text-sm font-medium">
                            <span>Ara Toplam:</span>
                            <span>{{ $cartTotal }} TL</span>
                        </div>
                        @if($discount > 0)
                            <div class="flex justify-between text-green-500 text-sm font-bold">
                                <span>İndirim:</span>
                                <span>-{{ $discount }} TL</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between font-bold text-xl text-brand-text mt-2 pt-2 border-t border-gray-100">
                            <span>Ödenecek Tutar:</span>
                            <span class="text-brand-gold">{{ max(0, $cartTotal - $discount) }} TL</span>
                        </div>
                    </div>

                    <button wire:click="checkout"
                        class="w-full py-4 bg-brand-gold hover:bg-[#735738] text-white rounded-xl font-bold transition-colors shadow-md text-lg relative overflow-hidden group">
                        <span class="relative z-10">Siparişi Tamamla</span>
                        <div
                            class="absolute inset-0 h-full w-full bg-white/20 transform -translate-x-full group-hover:translate-x-full transition-transform duration-500 ease-out">
                        </div>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>