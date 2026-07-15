<?php
use Livewire\Component;
use Livewire\Attributes\On;
use Darryldecode\Cart\Facades\CartFacade as Cart;

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

    public function with(): array
    {
        return [
            'cartItems' => Cart::getContent(),
            'cartTotal' => Cart::getTotal(),
        ];
    }
};
?>
<div>
    <div
        style="position: fixed; right: 0; top: 0; height: 100vh; width: 350px; background: #fff; box-shadow: -5px 0 15px rgba(0,0,0,0.1); padding: 20px; z-index: 9999; overflow-y: auto; font-family: sans-serif;">
        <div
            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 style="margin: 0; color: #333;">Sepetim</h2>
            <span style="background: #e74c3c; color: white; border-radius: 50%; padding: 2px 10px; font-weight: bold;">
                {{ $cartItems->count() }}
            </span>
        </div>

        @if($cartItems->isEmpty())
            <p style="text-align: center; color: #777; margin-top: 50px;">Sepetiniz şu an boş.</p>
        @else
            <ul style="list-style: none; padding: 0; margin: 0;">
                @foreach($cartItems as $item)
                    <li
                        style="display: flex; justify-content: space-between; align-items: center; background: #f9f9f9; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
                        <div>
                            <h4 style="margin: 0 0 5px 0; color: #333;">{{ $item->name }}</h4>
                            <span style="font-size: 14px; color: #666;">{{ $item->quantity }} x {{ $item->price }} TL</span>
                        </div>
                        <button wire:click="removeItem('{{ $item->id }}')"
                            style="background: none; border: none; color: #e74c3c; font-size: 20px; cursor: pointer;">
                            &times;
                        </button>
                    </li>
                @endforeach
            </ul>

            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px;">
                <div
                    style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-bottom: 20px; color: #333;">
                    <span>Toplam:</span>
                    <span>{{ $cartTotal }} TL</span>
                </div>
                <button wire:click="clearCart"
                    style="width: 100%; padding: 12px; background: #fdf2f2; color: #e74c3c; border: 1px solid #fad5d5; border-radius: 8px; font-weight: bold; cursor: pointer;">
                    Sepeti Temizle
                </button>
            </div>
        @endif
    </div>
</div>