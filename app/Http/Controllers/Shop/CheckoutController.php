<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ShippingMethod;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\SslCommerzService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public const DISTRICTS = [
        'Dhaka', 'Chattogram', 'Sylhet', 'Rajshahi', 'Khulna', 'Barishal', 'Rangpur', 'Mymensingh',
        'Comilla', 'Gazipur', 'Narayanganj', 'Bogura', 'Cox\'s Bazar', 'Jessore', 'Dinajpur', 'Tangail',
        'Feni', 'Noakhali', 'Pabna', 'Kushtia', 'Faridpur', 'Brahmanbaria', 'Habiganj', 'Moulvibazar',
        'Savar', 'Narsingdi', 'Kishoreganj', 'Manikganj', 'Munshiganj', 'Jamalpur', 'Sherpur', 'Netrokona',
        'Sirajganj', 'Natore', 'Naogaon', 'Chapainawabganj', 'Joypurhat', 'Thakurgaon', 'Panchagarh',
        'Nilphamari', 'Lalmonirhat', 'Kurigram', 'Gaibandha', 'Satkhira', 'Bagerhat', 'Narail', 'Magura',
        'Jhenaidah', 'Chuadanga', 'Meherpur', 'Patuakhali', 'Bhola', 'Pirojpur', 'Jhalokati', 'Barguna',
        'Chandpur', 'Lakshmipur', 'Khagrachhari', 'Rangamati', 'Bandarban', 'Sunamganj', 'Gopalganj',
        'Madaripur', 'Shariatpur', 'Rajbari',
    ];

    public function __construct(protected CartService $cart, protected OrderService $orders) {}

    public function index()
    {
        $cart = $this->cart->current();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('Your bag is empty.'));
        }
        if (! auth()->check() && setting('guest_checkout', '1') !== '1') {
            return redirect()->route('login', ['redirect' => route('checkout.index')]);
        }

        $problems = $this->cart->validateForCheckout();
        $cart = $this->cart->current();

        $user = auth()->user();
        $addresses = $user?->addresses()->orderByDesc('is_default')->get() ?? collect();
        $default = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $methods = ShippingMethod::availableFor($default?->district ?? 'Dhaka');

        return view('shop.checkout', [
            'cart' => $cart,
            'problems' => $problems,
            'addresses' => $addresses,
            'defaultAddress' => $default,
            'methods' => $methods,
            'districts' => self::DISTRICTS,
            'codEnabled' => setting('cod_enabled', '1') === '1',
            'sslEnabled' => setting('sslcommerz_enabled', '1') === '1',
        ]);
    }

    public function shippingMethods(Request $request): JsonResponse
    {
        $subtotal = $this->cart->current()->subtotal;
        $methods = ShippingMethod::availableFor($request->get('district'))->map(fn (ShippingMethod $m) => [
            'id' => $m->id,
            'name' => $m->name,
            'description' => $m->description,
            'eta' => $m->estimated_days,
            'cost' => $m->costFor($subtotal),
            'cost_formatted' => $m->costFor($subtotal) > 0 ? money($m->costFor($subtotal)) : __('Free'),
        ]);

        return response()->json($methods);
    }

    public function store(Request $request)
    {
        $allowed = array_filter(['cod' => setting('cod_enabled', '1') === '1', 'sslcommerz' => setting('sslcommerz_enabled', '1') === '1']);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'regex:/^(\+?88)?01[3-9]\d{8}$/'],
            'email' => ['nullable', 'email', 'max:150'],
            'address_line' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', Rule::in(self::DISTRICTS)],
            'postal_code' => ['nullable', 'string', 'max:10'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'payment_method' => ['required', Rule::in(array_keys($allowed))],
            'notes' => ['nullable', 'string', 'max:500'],
            'save_address' => ['nullable', 'boolean'],
            'create_account' => ['nullable', 'boolean'],
            'password' => ['required_if:create_account,1', 'nullable', 'string', 'min:8'],
            'terms' => ['accepted'],
        ], [
            'phone.regex' => __('Enter a valid Bangladeshi mobile number (e.g. 017XXXXXXXX).'),
            'terms.accepted' => __('Please accept the terms to continue.'),
        ]);

        $cart = $this->cart->current();
        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', __('Your bag is empty.'));
        }
        if ($problems = $this->cart->validateForCheckout()) {
            return back()->withInput()->with('error', implode(' ', $problems));
        }
        $cart = $this->cart->current();

        $shipping = ShippingMethod::findOrFail($data['shipping_method_id']);
        if (! ShippingMethod::availableFor($data['district'])->contains('id', $shipping->id)) {
            return back()->withInput()->with('error', __('That delivery option is not available for your district.'));
        }

        $user = auth()->user();

        // Optional account creation for guests.
        if (! $user && $request->boolean('create_account') && ! empty($data['email'])) {
            if (! \App\Models\User::where('email', $data['email'])->exists()) {
                $user = \App\Models\User::create([
                    'name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'], 'password' => $data['password'],
                ]);
                $user->assignRole('customer');
                auth()->login($user);
            }
        }

        $address = collect($data)->only(['name', 'phone', 'email', 'address_line', 'area', 'city', 'district', 'postal_code'])
            ->put('country', 'Bangladesh')->all();

        if ($user && $request->boolean('save_address')) {
            $user->addresses()->create(array_merge($address, [
                'label' => $user->addresses()->count() === 0 ? 'Home' : 'Address '.($user->addresses()->count() + 1),
                'is_default' => $user->addresses()->count() === 0,
            ]));
        }

        $order = $this->orders->place($cart, $address, $shipping, $data['payment_method'], $data['notes'] ?? null, $user);
        $this->cart->refresh();

        if ($order->payment_method === 'sslcommerz') {
            return $this->redirectToGateway($order);
        }

        session(['last_order' => $order->order_number]);

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function pay(Order $order)
    {
        abort_unless($this->canView($order), 403);
        if ($order->payment_status === 'paid') {
            return redirect()->route('checkout.success', $order->order_number);
        }

        return $this->redirectToGateway($order);
    }

    protected function redirectToGateway(Order $order)
    {
        try {
            $url = app(SslCommerzService::class)->createSession($order);
        } catch (\Throwable $e) {
            report($e);
            session(['last_order' => $order->order_number]);

            return redirect()->route('checkout.success', $order->order_number)
                ->with('error', __('We could not reach the payment gateway. Your order is saved — you can pay from the order page or switch to cash on delivery.'));
        }

        return redirect()->away($url);
    }

    public function success(Order $order)
    {
        abort_unless($this->canView($order), 403);
        $order->load('items', 'shippingMethod');

        return view('shop.order-success', compact('order'));
    }

    protected function canView(Order $order): bool
    {
        return (auth()->check() && $order->user_id === auth()->id())
            || session('last_order') === $order->order_number
            || auth()->user()?->isStaff();
    }
}
