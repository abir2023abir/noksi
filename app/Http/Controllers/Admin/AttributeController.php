<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('values')->orderBy('sort_order')->paginate(20);

        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:attributes,slug'],
            'type' => ['required', 'in:select,color,button'],
            'is_filterable' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_filterable'] = $request->boolean('is_filterable', true);

        Attribute::create($data);

        return redirect()->route('admin.attributes.index')->with('success', __('Attribute created.'));
    }

    public function edit(Attribute $attribute)
    {
        $attribute->load('values');

        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', Rule::unique('attributes', 'slug')->ignore($attribute->id)],
            'type' => ['required', 'in:select,color,button'],
            'is_filterable' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
        $data['is_filterable'] = $request->boolean('is_filterable');

        $attribute->update($data);

        return back()->with('success', __('Attribute updated.'));
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();

        return redirect()->route('admin.attributes.index')->with('success', __('Attribute deleted.'));
    }

    public function storeValue(Request $request, Attribute $attribute)
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:100'],
            'color_code' => ['nullable', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $attribute->values()->create($data);

        return back()->with('success', __('Value added.'));
    }

    public function destroyValue(Attribute $attribute, AttributeValue $value)
    {
        abort_unless($value->attribute_id === $attribute->id, 404);
        $value->delete();

        return back()->with('success', __('Value deleted.'));
    }
}
