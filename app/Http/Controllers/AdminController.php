<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Table;
use App\Models\Restaurant;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    protected function getActiveRestaurantId()
    {
        if (!session('is_developer') && Auth::check() && Auth::user()->restaurant_id) {
            return Auth::user()->restaurant_id;
        }

        if (session()->has('active_restaurant_id')) {
            $restaurantId = session('active_restaurant_id');
            if (Restaurant::where('id', $restaurantId)->exists()) {
                return $restaurantId;
            }
        }
        $default = Restaurant::first();
        if ($default) {
            session(['active_restaurant_id' => $default->id]);
            return $default->id;
        }
        return null;
    }

    public function index()
    {
        $restaurantId = $this->getActiveRestaurantId();
        $categoryCount = Schema::hasTable('categories') ? Category::where('restaurant_id', $restaurantId)->count() : 0;
        $productCount = Schema::hasTable('products') ? Product::where('restaurant_id', $restaurantId)->count() : 0;
        return view('admin.dashboard', compact('categoryCount', 'productCount'));
    }

    public function categories()
    {
        $restaurantId = $this->getActiveRestaurantId();
        if (Schema::hasTable('categories')) {
            $allCategories = Category::where('restaurant_id', $restaurantId)->get();
            $categories = $this->buildCategoryTree($allCategories);
        } else {
            $categories = collect();
        }
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'parent_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $restaurantId = $this->getActiveRestaurantId();
        $parentId = null;

        if ($request->filled('parent_name')) {
            $parent = Category::where('name', $request->parent_name)->where('restaurant_id', $restaurantId)->first();
            if (!$parent) {
                $parent = new Category();
                $parent->name = $request->parent_name;
                $parent->restaurant_id = $restaurantId;
                $parent->image_url = '';
                $parent->save();
            }
            $parentId = $parent->id;
        } else {
            $parentId = $request->filled('parent_id') ? $request->parent_id : null;
        }

        $category = new Category();
        $category->name = $request->name;
        $category->parent_id = $parentId;
        $category->restaurant_id = $restaurantId;

        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $category->image_url = 'images/' . $imageName;
        } else {
            $category->image_url = '';
        }

        $category->save();
        return redirect()->back()->with('success', 'Kategori başarıyla eklendi!');
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'parent_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request->name;

        $restaurantId = $this->getActiveRestaurantId();
        if ($request->filled('parent_name')) {
            if ($request->parent_name !== $category->name) {
                $parent = Category::where('name', $request->parent_name)->where('restaurant_id', $restaurantId)->first();
                if (!$parent) {
                    $parent = new Category();
                    $parent->name = $request->parent_name;
                    $parent->restaurant_id = $restaurantId;
                    $parent->image_url = '';
                    $parent->save();
                }
                $category->parent_id = $parent->id;
            }
        } else {
            if ($request->has('parent_id') && $request->parent_id != $id) {
                $category->parent_id = $request->filled('parent_id') ? $request->parent_id : null;
            }
        }

        if ($request->hasFile('image')) {
            if ($category->image_url && File::exists(public_path($category->image_url))) {
                File::delete(public_path($category->image_url));
            }
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $category->image_url = 'images/' . $imageName;
        }

        $category->save();
        return redirect()->back()->with('success', 'Kategori başarıyla güncellendi!');
    }

    private function buildCategoryTree($categories, $parentId = null, $depth = 0)
    {
        $branch = collect();
        $filtered = $categories->where('parent_id', $parentId)->sortBy('name');
        foreach ($filtered as $category) {
            $category->depth = $depth;
            $branch->push($category);
            $children = $this->buildCategoryTree($categories, $category->id, $depth + 1);
            foreach ($children as $child) {
                $branch->push($child);
            }
        }
        return $branch;
    }

    public function deleteCategory($id)
    {
        $category = Category::findOrFail($id);
        if ($category->image_url && File::exists(public_path($category->image_url))) {
            File::delete(public_path($category->image_url));
        }
        $category->delete();
        return redirect()->back()->with('success', 'Kategori silindi!');
    }

    public function tables()
    {
        $restaurantId = $this->getActiveRestaurantId();
        $tables = Schema::hasTable('tables') ? Table::where('restaurant_id', $restaurantId)->orderBy('id', 'desc')->get() : collect();
        return view('admin.tables', compact('tables'));
    }

    public function storeTable(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        Table::create([
            'name' => $request->name,
            'token' => Str::random(8),
            'restaurant_id' => $this->getActiveRestaurantId()
        ]);

        return redirect()->back()->with('success', 'Masa başarıyla oluşturuldu.');
    }

    public function deleteTable($id)
    {
        Table::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Masa silindi.');
    }

    public function products()
    {
        $restaurantId = $this->getActiveRestaurantId();
        $products = Schema::hasTable('products') ? Product::where('restaurant_id', $restaurantId)->orderBy('id', 'desc')->get() : collect();
        if (Schema::hasTable('categories')) {
            $allCategories = Category::where('restaurant_id', $restaurantId)->get();
            $categories = $this->buildCategoryTree($allCategories);
        } else {
            $categories = collect();
        }
        return view('admin.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'calories' => 'nullable|integer',
            'prep_time' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $product = new Product();
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->calories = $request->calories ?? 0;
        $product->prep_time = $request->prep_time ?? 15;
        $product->is_gluten_free = $request->has('is_gluten_free') ? 1 : 0;
        $product->restaurant_id = $this->getActiveRestaurantId();

        if ($request->hasFile('image')) {
            $imageName = time() . '_prod.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image_url = 'images/' . $imageName;
        } else {
            $product->image_url = '';
        }

        $product->save();
        if ($request->wantsJson()) {
            $product->load('category.parent');
            return response()->json([
                'status' => 'success',
                'message' => 'Ürün başarıyla eklendi!',
                'product' => $product
            ]);
        }
        return redirect()->back()->with('success', 'Ürün başarıyla eklendi!');
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'calories' => 'nullable|integer',
            'prep_time' => 'nullable|integer'
        ]);

        $product = Product::findOrFail($id);
        $product->category_id = $request->category_id;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->calories = $request->calories ?? 0;
        $product->prep_time = $request->prep_time ?? 15;
        $product->is_gluten_free = $request->has('is_gluten_free') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($product->image_url && File::exists(public_path($product->image_url))) {
                File::delete(public_path($product->image_url));
            }
            $imageName = time() . '_prod.' . $request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $product->image_url = 'images/' . $imageName;
        }

        $product->save();
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Ürün başarıyla güncellendi!',
                'product' => $product
            ]);
        }
        return redirect()->back()->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image_url && File::exists(public_path($product->image_url))) {
            File::delete(public_path($product->image_url));
        }
        $product->delete();
        if (request()->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Ürün silindi!'
            ]);
        }
        return redirect()->back()->with('success', 'Ürün silindi!');
    }

    public function orders()
    {
        return view('admin.orders');
    }

    public function settings()
    {
        $restaurantId = $this->getActiveRestaurantId();
        $settings = Schema::hasTable('settings') ? Setting::where('restaurant_id', $restaurantId)->pluck('value', 'key')->toArray() : [];
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        $textSettings = $request->except(['_token', 'logo', 'cover_image', 'delete_logo', 'delete_cover_image']);

        foreach ($textSettings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key, 'restaurant_id' => $restaurantId],
                ['value' => $value]
            );
        }

        if ($request->input('delete_logo') == '1' && !$request->hasFile('logo')) {
            $oldLogo = Setting::where('key', 'logo')->where('restaurant_id', $restaurantId)->value('value');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }
            Setting::updateOrCreate(['key' => 'logo', 'restaurant_id' => $restaurantId], ['value' => '']);
        } elseif ($request->hasFile('logo')) {
            $oldLogo = Setting::where('key', 'logo')->where('restaurant_id', $restaurantId)->value('value');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $logoName = 'logo_' . time() . '.' . $request->logo->extension();
            $request->logo->move(public_path('images/settings'), $logoName);
            Setting::updateOrCreate(['key' => 'logo', 'restaurant_id' => $restaurantId], ['value' => 'images/settings/' . $logoName]);
        }

        if ($request->input('delete_cover_image') == '1' && !$request->hasFile('cover_image')) {
            $oldCover = Setting::where('key', 'cover_image')->where('restaurant_id', $restaurantId)->value('value');
            if ($oldCover && File::exists(public_path($oldCover))) {
                File::delete(public_path($oldCover));
            }
            Setting::updateOrCreate(['key' => 'cover_image', 'restaurant_id' => $restaurantId], ['value' => '']);
        } elseif ($request->hasFile('cover_image')) {
            $oldCover = Setting::where('key', 'cover_image')->where('restaurant_id', $restaurantId)->value('value');
            if ($oldCover && File::exists(public_path($oldCover))) {
                File::delete(public_path($oldCover));
            }

            $coverName = 'cover_' . time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('images/settings'), $coverName);
            Setting::updateOrCreate(['key' => 'cover_image', 'restaurant_id' => $restaurantId], ['value' => 'images/settings/' . $coverName]);
        }

        return redirect()->back()->with('success', 'Restoran bilgileri başarıyla güncellendi.');
    }

    public function developerRestaurants()
    {
        if (!session('is_developer')) {
            return redirect('/login');
        }
        $restaurants = Restaurant::with('users')->orderBy('id', 'desc')->get();
        return view('admin.restaurants', compact('restaurants'));
    }

    public function storeRestaurant(Request $request)
    {
        if (!session('is_developer')) {
            return redirect('/login');
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:restaurants,slug|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:6'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $restaurant = new Restaurant();
            $restaurant->name = $request->name;
            $restaurant->slug = Str::slug($request->slug);
            $restaurant->phone = $request->phone;
            $restaurant->address = $request->address;

            if ($request->hasFile('logo')) {
                $logoName = time() . '_logo.' . $request->logo->extension();
                $request->logo->move(public_path('images/settings'), $logoName);
                $restaurant->logo_url = 'images/settings/' . $logoName;
            }

            if ($request->hasFile('cover_image')) {
                $coverName = time() . '_cover.' . $request->cover_image->extension();
                $request->cover_image->move(public_path('images/settings'), $coverName);
                $restaurant->cover_image_url = 'images/settings/' . $coverName;
            }

            $restaurant->save();

            $user = new \App\Models\User();
            $user->name = $restaurant->name . ' Admin';
            $user->email = $request->admin_email;
            $user->password = bcrypt($request->admin_password);
            $user->restaurant_id = $restaurant->id;
            $user->save();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Restoran başarıyla eklendi!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Restoran eklenirken bir hata oluştu: ' . $e->getMessage()])->withInput();
        }
    }

    public function updateRestaurant(Request $request, $id)
    {
        if (!session('is_developer')) {
            return redirect('/login');
        }
        $restaurant = Restaurant::findOrFail($id);
        $user = \App\Models\User::where('restaurant_id', $restaurant->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:restaurants,slug,' . $id . '|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'admin_email' => 'required|email|unique:users,email,' . ($user ? $user->id : ''),
            'admin_password' => 'nullable|string|min:6'
        ]);

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $restaurant->name = $request->name;
            $restaurant->slug = Str::slug($request->slug);
            $restaurant->phone = $request->phone;
            $restaurant->address = $request->address;

            if ($request->hasFile('logo')) {
                if ($restaurant->logo_url && File::exists(public_path($restaurant->logo_url))) {
                    File::delete(public_path($restaurant->logo_url));
                }
                $logoName = time() . '_logo.' . $request->logo->extension();
                $request->logo->move(public_path('images/settings'), $logoName);
                $restaurant->logo_url = 'images/settings/' . $logoName;
            }

            if ($request->hasFile('cover_image')) {
                if ($restaurant->cover_image_url && File::exists(public_path($restaurant->cover_image_url))) {
                    File::delete(public_path($restaurant->cover_image_url));
                }
                $coverName = time() . '_cover.' . $request->cover_image->extension();
                $request->cover_image->move(public_path('images/settings'), $coverName);
                $restaurant->cover_image_url = 'images/settings/' . $coverName;
            }

            $restaurant->save();

            if (!$user) {
                $user = new \App\Models\User();
                $user->name = $restaurant->name . ' Admin';
                $user->restaurant_id = $restaurant->id;
            }
            $user->email = $request->admin_email;
            if ($request->admin_password) {
                $user->password = bcrypt($request->admin_password);
            }
            $user->save();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->back()->with('success', 'Restoran başarıyla güncellendi!');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Restoran güncellenirken bir hata oluştu: ' . $e->getMessage()])->withInput();
        }
    }

    public function deleteRestaurant($id)
    {
        if (!session('is_developer')) {
            return redirect('/login');
        }
        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->logo_url && File::exists(public_path($restaurant->logo_url))) {
            File::delete(public_path($restaurant->logo_url));
        }
        if ($restaurant->cover_image_url && File::exists(public_path($restaurant->cover_image_url))) {
            File::delete(public_path($restaurant->cover_image_url));
        }
        $restaurant->delete();
        return redirect()->back()->with('success', 'Restoran silindi!');
    }

    public function selectRestaurant(Request $request)
    {
        $request->validate(['restaurant_id' => 'required|integer|exists:restaurants,id']);
        session(['active_restaurant_id' => $request->restaurant_id]);
        return redirect()->back()->with('success', 'Aktif restoran değiştirildi.');
    }

    public function resetTable($id)
    {
        $table = Table::findOrFail($id);
        $table->active_session_id = Str::random(40);
        $table->save();
        return redirect()->back()->with('success', 'Masa oturumu sıfırlandı.');
    }

    public function checkNewOrders(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        $latestOrder = \App\Models\Order::where('restaurant_id', $restaurantId)->latest('id')->first();
        $hasNew = false;
        if ($latestOrder) {
            if (!session()->has('last_checked_order_id')) {
                session(['last_checked_order_id' => $latestOrder->id]);
            } else {
                $lastCheckedId = session('last_checked_order_id', 0);
                if ($latestOrder->id > $lastCheckedId) {
                    $hasNew = true;
                    session(['last_checked_order_id' => $latestOrder->id]);
                }
            }
        }
        return response()->json(['has_new' => $hasNew]);
    }

    public function register(Request $request)
    {
        $restaurantId = $this->getActiveRestaurantId();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = \App\Models\Transaction::where('restaurant_id', $restaurantId);

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->orderBy('id', 'desc')->get();

        $totals = [
            'Nakit' => (clone $query)->where('payment_method', 'Nakit')->sum('amount'),
            'Kredi Kartı' => (clone $query)->where('payment_method', 'Kredi Kartı')->sum('amount'),
            'Cari Hesap' => (clone $query)->where('payment_method', 'Cari Hesap')->sum('amount'),
            'Yemek Fişi' => (clone $query)->where('payment_method', 'Yemek Fişi')->sum('amount'),
            'Online Ödeme' => (clone $query)->where('payment_method', 'Online Ödeme')->sum('amount'),
            'Ödenmez' => (clone $query)->where('payment_method', 'Ödenmez')->sum('amount'),
            'grand_total' => (clone $query)->sum('amount'),
        ];

        return view('admin.register', compact('transactions', 'totals', 'startDate', 'endDate'));
    }
}