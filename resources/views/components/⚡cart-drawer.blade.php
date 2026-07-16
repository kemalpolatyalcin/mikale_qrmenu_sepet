<?php
use Livewire\Component;
use Livewire\Attributes\On;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\Order;
use App\Models\OrderItem;

new class extends Component {
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

    public function updateQuantity($id, $action)
    {
        if ($action === 'increase') {
            Cart::update($id, ['quantity' => 1]);
        } elseif ($action === 'decrease') {
            Cart::update($id, ['quantity' => -1]);
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
        $this->dispatch('cartUpdated');
    }

    public function checkout()
    {
        if (Cart::isEmpty()) {
            return;
        }

        $order = Order::create([
            'table_number' => 'Masa 1',
            'total_amount' => Cart::getTotal(),
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
        $this->dispatch('cartUpdated');
        $this->dispatch('close-cart');
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

<div x-data="{ open: false }" @open-cart.window="open = true" @close-cart.window="open = false">

    <div x-show="open" x-transition.opacity.duration.300ms @click="open = false"
        class="fixed inset-0 bg-black/50 z-[90] backdrop-blur-sm" style="display: none;">
    </div>

    <div :class="open ? 'translate-x-0' : 'translate-x-full'"
        class="fixed top-0 right-0 h-full w-full sm:w-[400px] bg-brand-bg shadow-2xl z-[100] transform transition-transform duration-300 ease-in-out flex flex-col font-sans">

        <div class="flex justify-between items-center px-6 py-5 bg-white border-b border-gray-100 shrink-0">
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

        <div class="flex-1 overflow-y-auto p-6 no-scrollbar relative">
            @if($cartItems->isEmpty())
                <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                    <i class="fa-solid fa-basket-shopping text-5xl mb-4 opacity-20"></i>
                    <p class="font-medium text-gray-500">Sepetiniz şu an boş.</p>
                </div>
            @else
                <ul class="space-y-4 m-0 p-0 list-none">
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
                                        class="px-3 py-1 text-gray-500 hover:text-brand-text font-bold transition-colors">
                                        -
                                    </button>
                                    <span class="px-2 font-semibold text-sm w-8 text-center">{{ $item->quantity }}</span>
                                    <button wire:click="updateQuantity('{{ $item->id }}', 'increase')"
                                        class="px-3 py-1 text-gray-500 hover:text-brand-text font-bold transition-colors">
                                        +
                                    </button>
                                </div>
                                <span class="font-bold text-gray-800">{{ $item->price * $item->quantity }} TL</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if(!$cartItems->isEmpty())
            <div class="p-6 bg-white border-t border-gray-100 shrink-0 shadow-[0_-5px_15px_rgba(0,0,0,0.02)]">
                <div class="flex justify-between font-bold text-lg text-brand-text mb-4">
                    <span>Genel Toplam:</span>
                    <span class="text-brand-gold">{{ $cartTotal }} TL</span>
                </div>

                <button wire:click="checkout"
                    class="w-full py-3 bg-brand-gold hover:bg-[#735738] text-white rounded-xl font-bold transition-colors shadow-md mb-3">
                    Siparişi Tamamla
                </button>

                <button wire:click="clearCart"
                    class="w-full py-2 bg-red-50 hover:bg-red-100 text-red-500 rounded-xl font-semibold transition-colors text-sm">
                    Sepeti Temizle
                </button>
            </div>
        @endif
    </div>
</div>