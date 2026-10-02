<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Invoice') }} {{ $order->order_number }} — NOKSHI</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', system-ui, sans-serif; color: #1a1220; background: #fff; font-size: 14px; line-height: 1.5; }
        .wrap { max-width: 800px; margin: 0 auto; padding: 40px 32px; }
        .brand { font-family: Georgia, serif; font-size: 28px; letter-spacing: 0.04em; color: #5B1B4F; }
        .muted { color: #6b6170; }
        .row { display: flex; justify-content: space-between; gap: 24px; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        h2 { font-size: 12px; text-transform: uppercase; letter-spacing: 0.12em; color: #5B1B4F; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 24px; }
        th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #6b6170; border-bottom: 2px solid #1a1220; padding: 8px 0; }
        td { padding: 12px 0; border-bottom: 1px solid #eee; vertical-align: top; }
        .right { text-align: right; }
        .totals { margin-top: 16px; margin-left: auto; width: 260px; }
        .totals div { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .grand { font-weight: 700; font-size: 16px; border-top: 2px solid #1a1220; margin-top: 8px; padding-top: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 999px; background: #f3e8f0; color: #5B1B4F; font-size: 11px; font-weight: 600; text-transform: capitalize; }
        .actions { margin-bottom: 24px; display: flex; gap: 8px; }
        .actions button, .actions a { appearance: none; border: 1px solid #1a1220; background: #1a1220; color: #fff; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .actions a.secondary { background: #fff; color: #1a1220; }
        @media print {
            .actions { display: none; }
            .wrap { padding: 0; }
        }
    </style>
</head>
<body>
@php $a = $order->shipping_address; @endphp
<div class="wrap">
    <div class="actions">
        <button onclick="window.print()">{{ __('Print / Save PDF') }}</button>
        <a href="{{ url()->previous() }}" class="secondary">{{ __('Back') }}</a>
    </div>

    <div class="row" style="align-items: flex-start; margin-bottom: 40px;">
        <div>
            <div class="brand">NOKSHI</div>
            <p class="muted" style="margin-top: 4px;">{{ setting('site_tagline', 'Wear the story.') }}</p>
            <p class="muted" style="margin-top: 12px; font-size: 12px;">
                {{ setting('contact_address', 'Dhaka, Bangladesh') }}<br>
                {{ setting('contact_phone', '') }} · {{ setting('contact_email', 'hello@nokshi.test') }}
            </p>
        </div>
        <div class="right">
            <p style="font-size: 22px; font-weight: 700;">{{ __('Invoice') }}</p>
            <p style="margin-top: 8px; font-family: ui-monospace, monospace; font-size: 15px;">{{ $order->order_number }}</p>
            <p class="muted" style="margin-top: 4px;">{{ $order->created_at->format('d M Y') }}</p>
            <p style="margin-top: 8px;"><span class="badge">{{ $order->status }}</span></p>
        </div>
    </div>

    <div class="grid-2" style="margin-bottom: 32px;">
        <div>
            <h2>{{ __('Bill to') }}</h2>
            <p style="font-weight: 600;">{{ $a['name'] ?? '' }}</p>
            <p class="muted">{{ $a['phone'] ?? '' }}</p>
            @if(!empty($a['email']))<p class="muted">{{ $a['email'] }}</p>@endif
            <p class="muted" style="margin-top: 6px;">
                {{ $a['address_line'] ?? '' }}@if(!empty($a['area'])), {{ $a['area'] }}@endif<br>
                {{ $a['city'] ?? '' }}, {{ $a['district'] ?? '' }} {{ $a['postal_code'] ?? '' }}
            </p>
        </div>
        <div>
            <h2>{{ __('Payment') }}</h2>
            <p>{{ $order->payment_label }}</p>
            <p class="muted" style="text-transform: capitalize;">{{ __('Status') }}: {{ $order->payment_status }}</p>
            @if($order->shippingMethod)
                <p style="margin-top: 12px;"><strong>{{ __('Delivery') }}:</strong> {{ $order->shippingMethod->name }}</p>
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ __('Item') }}</th>
                <th class="right">{{ __('Qty') }}</th>
                <th class="right">{{ __('Price') }}</th>
                <th class="right">{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <strong>{{ $item->name }}</strong>
                        @if($item->variant_label)<br><span class="muted">{{ $item->variant_label }}</span>@endif
                        @if($item->sku)<br><span class="muted" style="font-size: 11px;">SKU: {{ $item->sku }}</span>@endif
                    </td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">{{ money($item->price) }}</td>
                    <td class="right">{{ money($item->total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span class="muted">{{ __('Subtotal') }}</span><span>{{ money($order->subtotal) }}</span></div>
        @if($order->discount > 0)
            <div><span class="muted">{{ __('Discount') }}</span><span>−{{ money($order->discount) }}</span></div>
        @endif
        <div><span class="muted">{{ __('Delivery') }}</span><span>{{ $order->shipping_cost > 0 ? money($order->shipping_cost) : __('Free') }}</span></div>
        @if($order->tax > 0)
            <div><span class="muted">{{ __('Tax') }}</span><span>{{ money($order->tax) }}</span></div>
        @endif
        <div class="grand"><span>{{ __('Total') }}</span><span>{{ money($order->total) }}</span></div>
    </div>

    <p class="muted" style="margin-top: 48px; font-size: 12px; text-align: center;">
        {{ __('Thank you for shopping with NOKSHI. For support:') }} {{ setting('contact_email', 'hello@nokshi.test') }}
    </p>
</div>
</body>
</html>
