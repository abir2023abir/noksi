<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\BlogCategory;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\FlashSale;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    protected function u(string $id, int $w = 1600): string
    {
        return "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q=80";
    }

    public function run(): void
    {
        /* ---------- Hero banners ---------- */
        $banners = [
            ['The Eid Edit', 'Luminous jamdani, hand-spun khadi and everything in between.', 'New collection', 'Shop the edit', '/collections/eid-edit-2026', '#5B1B4F', '#F5C24B', '1583391733956-3750e0ff4e8b'],
            ['Monsoon Linen', 'Breathable layers for the wettest months of the year.', 'Just landed', 'Explore linen', '/collections/monsoon-linen', '#0F766E', '#F4EFE6', '1591369822096-ffd140ec948f'],
            ['Everyday Uniform', 'Tees, shirts and trousers that do the heavy lifting.', 'Essentials', 'Build yours', '/collections/everyday-uniform', '#C2410C', '#F4EFE6', '1600950207944-0d63e8edbc3f'],
        ];
        foreach ($banners as $i => [$title, $sub, $eyebrow, $btn, $url, $bg, $fg, $img]) {
            Banner::firstOrCreate(['title' => $title, 'position' => 'hero'], [
                'subtitle' => $sub, 'eyebrow' => $eyebrow, 'button_text' => $btn, 'button_url' => $url,
                'bg_color' => $bg, 'text_color' => $fg, 'image' => $this->u($img), 'sort_order' => $i,
            ]);
        }
        Banner::firstOrCreate(['title' => 'Handloom, honestly priced', 'position' => 'promo'], [
            'subtitle' => 'Every saree in our Heritage line names the weaver who made it.',
            'eyebrow' => 'Nokshi Heritage', 'button_text' => 'Meet the makers', 'button_url' => '/pages/our-story',
            'bg_color' => '#1B0E2B', 'text_color' => '#F5C24B', 'image' => $this->u('1617627143750-d86bc21e42bb'),
        ]);

        /* ---------- Testimonials ---------- */
        $testimonials = [
            ['Nusrat Jahan', 'Architect, Dhanmondi', 'I have stopped buying sarees anywhere else. The jamdani I got for my sister\'s holud was the talk of the evening.', 5],
            ['Tanvir Hossain', 'Software engineer, Uttara', 'Finally a local brand that fits tall guys. The chore jacket is my daily now — three months in and it looks better than day one.', 5],
            ['Farzana Karim', 'Teacher, Chattogram', 'Ordered on Tuesday, wearing it Thursday. The kurti colours are exactly like the photos, which never happens.', 5],
            ['Sadia Islam', 'Student, Sylhet', 'The linen shirt dress is so comfortable I bought it in two colours. Customer service on WhatsApp was quick and kind.', 4],
        ];
        foreach ($testimonials as $i => [$name, $role, $body, $rating]) {
            Testimonial::firstOrCreate(['name' => $name], ['role' => $role, 'body' => $body, 'rating' => $rating, 'sort_order' => $i]);
        }

        /* ---------- Shipping ---------- */
        $dhaka = ShippingZone::firstOrCreate(['name' => 'Inside Dhaka'], ['districts' => ['Dhaka']]);
        $outside = ShippingZone::firstOrCreate(['name' => 'Outside Dhaka'], ['districts' => []]);
        ShippingMethod::firstOrCreate(['name' => 'Standard — Inside Dhaka'], [
            'shipping_zone_id' => $dhaka->id, 'cost' => 70, 'free_above' => 2000, 'estimated_days' => '1–2 days', 'sort_order' => 1,
            'description' => 'Delivered by our own riders within Dhaka city.',
        ]);
        ShippingMethod::firstOrCreate(['name' => 'Express — Inside Dhaka'], [
            'shipping_zone_id' => $dhaka->id, 'cost' => 150, 'estimated_days' => 'Same day', 'sort_order' => 2,
            'description' => 'Order before 2 PM for same-day delivery.',
        ]);
        ShippingMethod::firstOrCreate(['name' => 'Nationwide Courier'], [
            'shipping_zone_id' => $outside->id, 'cost' => 130, 'free_above' => 4000, 'estimated_days' => '3–5 days', 'sort_order' => 3,
            'description' => 'Via Pathao / Steadfast to all 64 districts.',
        ]);

        /* ---------- Coupons ---------- */
        Coupon::firstOrCreate(['code' => 'WELCOME10'], ['description' => '10% off your first order', 'type' => 'percent', 'value' => 10, 'min_order' => 1000, 'max_discount' => 500, 'per_user_limit' => 1]);
        Coupon::firstOrCreate(['code' => 'EID500'], ['description' => '৳500 off orders above ৳5,000', 'type' => 'fixed', 'value' => 500, 'min_order' => 5000, 'expires_at' => now()->addDays(30)]);
        Coupon::firstOrCreate(['code' => 'FREESHIP'], ['description' => 'Free shipping nationwide', 'type' => 'free_shipping', 'value' => 0, 'min_order' => 1500]);

        /* ---------- Flash sale ---------- */
        $sale = FlashSale::firstOrCreate(['title' => 'Midweek Drop'], [
            'subtitle' => '48 hours. Selected pieces. Then they are gone.', 'color' => '#C2410C',
            'starts_at' => now()->subHours(6), 'ends_at' => now()->addHours(42),
        ]);
        if ($sale->items()->count() === 0) {
            Product::inRandomOrder()->limit(6)->get()->each(function (Product $p) use ($sale) {
                $sale->items()->create([
                    'product_id' => $p->id,
                    'sale_price' => round($p->price * 0.7, -1),
                    'quantity_limit' => 20,
                    'sold' => rand(3, 14),
                ]);
            });
        }

        /* ---------- Pages ---------- */
        $pages = [
            ['Our Story', 'our-story', '<h2>Made in Dhaka, worn everywhere</h2><p>NOKSHI began in 2019 in a two-room flat in Lalmatia with a single loom, a borrowed sewing machine and a stubborn idea: that clothing made here could be as considered as anything imported — and far more honest about who made it.</p><p>Today we work with 40+ weavers, tailors and embroiderers across Rupganj, Tangail, Jessore and Narayanganj. Every Heritage piece carries the name of its maker on the tag.</p><h3>What we believe</h3><ul><li><strong>Natural first.</strong> Cotton, linen, silk, khadi and jute wherever possible.</li><li><strong>Small batches.</strong> We make what we can sell, and we sell what we make.</li><li><strong>Fair pay.</strong> Our makers earn above the living wage benchmark for their district.</li></ul>'],
            ['Shipping & Delivery', 'shipping', '<p>We deliver to all 64 districts of Bangladesh.</p><ul><li><strong>Inside Dhaka:</strong> 1–2 business days, ৳70 (free over ৳2,000). Same-day express available before 2 PM.</li><li><strong>Outside Dhaka:</strong> 3–5 business days via courier, ৳130 (free over ৳4,000).</li></ul><p>You will receive an SMS with a tracking link once your parcel leaves our studio.</p>'],
            ['Returns & Exchanges', 'returns', '<p>Not the right fit? You have <strong>7 days</strong> from delivery to request an exchange or return.</p><ul><li>Items must be unworn, unwashed and with tags attached.</li><li>Exchanges are free inside Dhaka; a ৳130 courier fee applies elsewhere.</li><li>Refunds are processed within 5 business days to your original payment method or as store credit.</li></ul><p>To start a return, message us on WhatsApp with your order number.</p>'],
            ['Size Guide', 'size-guide', '<p>Measurements are in inches. When between sizes, size up for a relaxed fit.</p><table><thead><tr><th>Size</th><th>Chest</th><th>Waist</th><th>Hip</th></tr></thead><tbody><tr><td>XS</td><td>32–33</td><td>25–26</td><td>34–35</td></tr><tr><td>S</td><td>34–35</td><td>27–28</td><td>36–37</td></tr><tr><td>M</td><td>36–38</td><td>29–31</td><td>38–40</td></tr><tr><td>L</td><td>39–41</td><td>32–34</td><td>41–43</td></tr><tr><td>XL</td><td>42–44</td><td>35–37</td><td>44–46</td></tr><tr><td>XXL</td><td>45–47</td><td>38–40</td><td>47–49</td></tr></tbody></table>'],
            ['Privacy Policy', 'privacy-policy', '<p>We collect only what we need to fulfil your order — your name, contact details and delivery address. We never sell your data. Payment details are handled entirely by SSLCommerz and never touch our servers.</p>'],
            ['Terms & Conditions', 'terms', '<p>By placing an order you agree to our shipping, returns and privacy policies. Prices are in Bangladeshi Taka and include VAT. We reserve the right to cancel orders in case of stock errors, in which case you will be refunded in full.</p>'],
        ];
        foreach ($pages as [$title, $slug, $body]) {
            Page::firstOrCreate(['slug' => $slug], ['title' => $title, 'body' => $body]);
        }

        /* ---------- FAQs ---------- */
        $faqs = [
            ['Orders', 'How do I track my order?', 'Use the tracking link in your confirmation SMS/email, or enter your order number on our Track Order page.'],
            ['Orders', 'Can I change or cancel my order?', 'Yes — as long as it has not shipped. Message us on WhatsApp with your order number and we will sort it out.'],
            ['Payment', 'What payment methods do you accept?', 'Cash on delivery anywhere in Bangladesh, plus bKash, Nagad, Rocket and all major cards via SSLCommerz.'],
            ['Delivery', 'How long does delivery take?', '1–2 days inside Dhaka, 3–5 days elsewhere. Same-day express is available inside Dhaka for orders placed before 2 PM.'],
            ['Returns', 'What is your exchange policy?', 'Seven days from delivery for unworn items with tags. Exchanges are free inside Dhaka.'],
            ['Products', 'Are your fabrics really handloom?', 'Everything in the Heritage line is hand-woven and we name the weaver on the tag. Our Basics and Street lines use mill-made natural fibres.'],
        ];
        foreach ($faqs as $i => [$cat, $q, $a]) {
            Faq::firstOrCreate(['question' => $q], ['category' => $cat, 'answer' => $a, 'sort_order' => $i]);
        }

        /* ---------- Blog ---------- */
        $author = User::role('super_admin')->first();
        $style = BlogCategory::firstOrCreate(['slug' => 'style'], ['name' => 'Style']);
        $craft = BlogCategory::firstOrCreate(['slug' => 'craft'], ['name' => 'Craft & Makers']);
        $care = BlogCategory::firstOrCreate(['slug' => 'care'], ['name' => 'Care']);

        $posts = [
            [$craft, 'Eleven days on the loom: how a Jamdani is made', 'We spent a week in Rupganj with master weaver Abdul Khalek to understand why a single saree takes almost two weeks.', '1609709295948-17d77cb2a69b', true],
            [$style, 'Five ways to wear a panjabi that are not Eid', 'The panjabi is the most versatile thing in a Bangladeshi wardrobe. Here is how to take it beyond the festive table.', '1626497764746-6dc36546b388', true],
            [$care, 'How to wash linen so it lasts a decade', 'Linen gets softer every wash — if you treat it right. Our studio manager shares the routine.', '1591369822096-ffd140ec948f', false],
            [$style, 'The monsoon capsule: eight pieces, thirty outfits', 'Humidity-proof dressing without repeating yourself for a month.', '1515886657613-9f3515b0c78f', false],
        ];
        foreach ($posts as $i => [$cat, $title, $excerpt, $img, $featured]) {
            Post::firstOrCreate(['slug' => str($title)->slug()], [
                'blog_category_id' => $cat->id, 'user_id' => $author?->id, 'title' => $title, 'excerpt' => $excerpt,
                'image' => $this->u($img, 1200), 'is_featured' => $featured, 'status' => 'published',
                'published_at' => now()->subDays(3 + $i * 6),
                'tags' => ['nokshi', str($cat->name)->lower()->toString()],
                'body' => "<p>{$excerpt}</p><p>Lorem ipsum is not our style, so here is something real: the best clothing is the clothing you forget you are wearing. Everything we make at NOKSHI starts from that idea — fabric first, then silhouette, then colour.</p><h2>Why it matters</h2><p>When you know who made your clothes, you wear them differently. You mend instead of replace. You pass them on. That is the quiet revolution we are interested in.</p><blockquote>“A garment should carry a story worth telling twice.”</blockquote><p>We will keep documenting our makers, our mistakes and our process here. Thanks for reading.</p>",
            ]);
        }
    }
}
