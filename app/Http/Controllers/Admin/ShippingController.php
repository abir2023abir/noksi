<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::with('methods')->latest()->get();
        $methods = ShippingMethod::with('zone')->orderBy('sort_order')->get();

        return view('admin.shipping.index', compact('zones', 'methods'));
    }

    public function storeZone(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'districts' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['districts'] = $this->parseDistricts($request->input('districts'));
        $data['is_active'] = $request->boolean('is_active', true);

        ShippingZone::create($data);

        return back()->with('success', __('Shipping zone created.'));
    }

    public function updateZone(Request $request, ShippingZone $zone)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'districts' => ['nullable'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['districts'] = $this->parseDistricts($request->input('districts'));
        $data['is_active'] = $request->boolean('is_active');

        $zone->update($data);

        return back()->with('success', __('Shipping zone updated.'));
    }

    public function destroyZone(ShippingZone $zone)
    {
        $zone->delete();

        return back()->with('success', __('Shipping zone deleted.'));
    }

    public function storeMethod(Request $request)
    {
        $data = $this->methodValidated($request);
        $data['is_active'] = $request->boolean('is_active', true);

        ShippingMethod::create($data);

        return back()->with('success', __('Shipping method created.'));
    }

    public function updateMethod(Request $request, ShippingMethod $method)
    {
        $data = $this->methodValidated($request);
        $data['is_active'] = $request->boolean('is_active');

        $method->update($data);

        return back()->with('success', __('Shipping method updated.'));
    }

    public function destroyMethod(ShippingMethod $method)
    {
        $method->delete();

        return back()->with('success', __('Shipping method deleted.'));
    }

    protected function methodValidated(Request $request): array
    {
        return $request->validate([
            'shipping_zone_id' => ['nullable', 'exists:shipping_zones,id'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:255'],
            'cost' => ['required', 'numeric', 'min:0'],
            'free_above' => ['nullable', 'numeric', 'min:0'],
            'estimated_days' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    protected function parseDistricts(mixed $districts): ?array
    {
        if (is_array($districts)) {
            return array_values(array_filter(array_map('trim', $districts)));
        }
        if (is_string($districts) && $districts !== '') {
            return array_values(array_filter(array_map('trim', preg_split('/[,\\n]+/', $districts))));
        }

        return null;
    }
}
