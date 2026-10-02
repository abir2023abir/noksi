<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CatalogSeeder extends Seeder
{
    protected function u(string $id, int $w = 900): string
    {
        return "https://images.unsplash.com/photo-{$id}?auto=format&fit=crop&w={$w}&q=80";
    }

    public function run(): void
    {
        /* ---------- Categories ---------- */
        $tree = [
            ['Women', 'নারী', '#C2185B', $this->u('1583391733956-3750e0ff4e8b'), [
                ['Sarees', 'শাড়ি', $this->u('1617627143750-d86bc21e42bb')],
                ['Kurtis', 'কুর্তি', $this->u('1597983073493-88cd35cf93b0')],
                ['Salwar Kameez', 'সালোয়ার কামিজ', $this->u('1622122201714-77da0ca8e5d2')],
                ['Dresses', 'ড্রেস', $this->u('1612722432474-b971cdcea546')],
                ['Tops & Blouses', 'টপস', $this->u('1581044777550-4cfa60707c03')],
            ]],
            ['Men', 'পুরুষ', '#1F3A5F', $this->u('1600950207944-0d63e8edbc3f'), [
                ['Panjabi', 'পাঞ্জাবি', $this->u('1609709295948-17d77cb2a69b')],
                ['Shirts', 'শার্ট', $this->u('1588359348347-9bc6cbbb689e')],
                ['T-Shirts', 'টি-শার্ট', $this->u('1583743814966-8936f5b7be1a')],
                ['Denim & Trousers', 'প্যান্ট', $this->u('1604176354204-9268737828e4')],
                ['Jackets', 'জ্যাকেট', $this->u('1552374196-1ab2a1c593e8')],
            ]],
            ['Kids', 'শিশু', '#F57C00', $this->u('1471286174890-9c112ffca5b4'), [
                ['Girls', 'মেয়ে', $this->u('1503919545889-aef636e10ad4')],
                ['Boys', 'ছেলে', $this->u('1519238263530-99bdd11df2ea')],
            ]],
            ['Accessories', 'অ্যাক্সেসরিজ', '#00796B', $this->u('1606760227091-3dd870d97f1d'), [
                ['Scarves & Ornas', 'ওড়না', $this->u('1601924994987-69e26d50dc26')],
                ['Bags', 'ব্যাগ', $this->u('1590874103328-eac38a683ce7')],
            ]],
        ];

        $cats = [];
        foreach ($tree as $i => [$name, $bn, $color, $img, $children]) {
            $parent = Category::firstOrCreate(['slug' => str($name)->slug()], [
                'name' => $name, 'name_bn' => $bn, 'color' => $color, 'image' => $img,
                'is_featured' => true, 'sort_order' => $i,
                'description' => "Explore our {$name} edit — considered pieces for every day and every occasion.",
            ]);
            foreach ($children as $j => [$cname, $cbn, $cimg]) {
                $cats[$cname] = Category::firstOrCreate(['slug' => str($cname)->slug()], [
                    'name' => $cname, 'name_bn' => $cbn, 'parent_id' => $parent->id, 'image' => $cimg,
                    'sort_order' => $j, 'color' => $color,
                ]);
            }
        }

        /* ---------- Brands ---------- */
        $brands = collect(['Nokshi Atelier', 'Nokshi Basics', 'Nokshi Heritage', 'Nokshi Street'])
            ->mapWithKeys(fn ($b) => [$b => Brand::firstOrCreate(['slug' => str($b)->slug()], ['name' => $b, 'is_featured' => true])]);

        /* ---------- Attributes ---------- */
        $size = Attribute::firstOrCreate(['slug' => 'size'], ['name' => 'Size', 'type' => 'button', 'sort_order' => 1]);
        foreach (['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size'] as $i => $s) {
            $size->values()->firstOrCreate(['slug' => str($s)->slug()], ['value' => $s, 'sort_order' => $i]);
        }
        $color = Attribute::firstOrCreate(['slug' => 'color'], ['name' => 'Colour', 'type' => 'color', 'sort_order' => 2]);
        $colors = [
            'Ivory' => '#F4EFE6', 'Black' => '#111111', 'Marigold' => '#F5C24B', 'Plum' => '#5B1B4F',
            'Teal' => '#0F766E', 'Terracotta' => '#C2410C', 'Indigo' => '#312E81', 'Sage' => '#84A98C',
            'Blush' => '#F4B6C2', 'Charcoal' => '#374151', 'Maroon' => '#7F1D1D', 'Sky' => '#7DD3FC',
        ];
        foreach (array_keys($colors) as $i => $c) {
            $color->values()->firstOrCreate(['slug' => str($c)->slug()], ['value' => $c, 'color_code' => $colors[$c], 'sort_order' => $i]);
        }
        $fabric = Attribute::firstOrCreate(['slug' => 'fabric'], ['name' => 'Fabric', 'type' => 'select', 'sort_order' => 3]);
        foreach (['Cotton', 'Linen', 'Silk', 'Muslin', 'Jamdani', 'Denim', 'Khadi', 'Viscose'] as $i => $f) {
            $fabric->values()->firstOrCreate(['slug' => str($f)->slug()], ['value' => $f, 'sort_order' => $i]);
        }

        /* ---------- Collections ---------- */
        $collections = [
            'Eid Edit 2026' => ['Festive, unhurried, luminous.', '#5B1B4F', $this->u('1583391733956-3750e0ff4e8b', 1400)],
            'Monsoon Linen' => ['Breathable pieces for the rainy months.', '#0F766E', $this->u('1591369822096-ffd140ec948f', 1400)],
            'Everyday Uniform' => ['The tees, shirts and trousers you reach for daily.', '#C2410C', $this->u('1600950207944-0d63e8edbc3f', 1400)],
        ];
        $coll = [];
        $i = 0;
        foreach ($collections as $name => [$sub, $col, $img]) {
            $coll[$name] = Collection::firstOrCreate(['slug' => str($name)->slug()], [
                'name' => $name, 'subtitle' => $sub, 'color' => $col, 'image' => $img, 'sort_order' => $i++,
            ]);
        }

        /* ---------- Products ---------- */
        $sizesStd = ['S', 'M', 'L', 'XL'];
        $products = [
            // Women — Sarees
            ['Dhakai Jamdani Saree — Marigold', 'Sarees', 'Nokshi Heritage', 8900, 11500, ['Marigold', 'Plum'], ['Free Size'], 'Jamdani', 'women',
                ['1617627143750-d86bc21e42bb', '1583391733956-3750e0ff4e8b', '1609709295948-17d77cb2a69b'],
                'Hand-loomed in Rupganj over eleven days, this Jamdani carries the classic paisley buti in a marigold that glows under evening light.',
                ['is_featured' => true, 'is_new' => true, 'badge' => 'Handloom', 'badge_color' => '#F5C24B'], 'Eid Edit 2026'],
            ['Cotton Tant Saree — Sage Stripe', 'Sarees', 'Nokshi Heritage', 3200, null, ['Sage', 'Ivory'], ['Free Size'], 'Cotton', 'women',
                ['1583391733956-3750e0ff4e8b', '1617627143750-d86bc21e42bb'],
                'Crisp Tangail cotton with a fine sage stripe. Pre-starched, breathable, and made for long humid days.',
                ['is_trending' => true], 'Monsoon Linen'],
            ['Half-Silk Saree — Plum Ombre', 'Sarees', 'Nokshi Atelier', 5400, 6800, ['Plum', 'Maroon'], ['Free Size'], 'Silk', 'women',
                ['1610030469983-98e550d6193c', '1617627143750-d86bc21e42bb'],
                'A gentle plum-to-maroon ombre on half-silk with a hand-finished zari border.',
                ['is_featured' => true], 'Eid Edit 2026'],

            // Women — Kurtis
            ['Block-Print Kurti — Indigo Ferns', 'Kurtis', 'Nokshi Atelier', 1850, 2400, ['Indigo', 'Teal'], $sizesStd, 'Cotton', 'women',
                ['1597983073493-88cd35cf93b0', '1609709295948-17d77cb2a69b'],
                'Hand block-printed ferns on soft mul cotton. Side slits, three-quarter sleeves, and pockets — because obviously.',
                ['is_new' => true, 'is_trending' => true], 'Monsoon Linen'],
            ['A-Line Linen Kurti — Terracotta', 'Kurtis', 'Nokshi Basics', 2100, null, ['Terracotta', 'Ivory', 'Sage'], $sizesStd, 'Linen', 'women',
                ['1585487000160-6ebcfceb0d03', '1609709295948-17d77cb2a69b'],
                'A relaxed A-line cut in washed linen with a mandarin collar and wooden buttons.',
                ['is_featured' => true], 'Monsoon Linen'],
            ['Embroidered Muslin Kurti — Ivory', 'Kurtis', 'Nokshi Heritage', 3600, 4200, ['Ivory', 'Blush'], $sizesStd, 'Muslin', 'women',
                ['1622122201714-77da0ca8e5d2', '1597983073493-88cd35cf93b0'],
                'Fine Dhaka muslin with tonal chikankari embroidery at the yoke. Comes with a matching slip.',
                ['badge' => 'Limited', 'badge_color' => '#C2185B'], 'Eid Edit 2026'],

            // Women — Salwar Kameez
            ['Three-Piece Set — Sky Chanderi', 'Salwar Kameez', 'Nokshi Atelier', 4800, 5900, ['Sky', 'Blush'], $sizesStd, 'Silk', 'women',
                ['1609709295948-17d77cb2a69b', '1597983073493-88cd35cf93b0'],
                'Unstitched Chanderi kameez, cotton salwar and a georgette dupatta with hand-tacked sequins.',
                ['is_new' => true], 'Eid Edit 2026'],
            ['Daily Cotton Set — Charcoal', 'Salwar Kameez', 'Nokshi Basics', 2600, null, ['Charcoal', 'Teal'], $sizesStd, 'Cotton', 'women',
                ['1590330297626-d7aff25a0431', '1618932260643-eee4a2f652a6'],
                'Stitched straight-cut kameez with matching palazzo. Zero fuss, full comfort.',
                [], 'Everyday Uniform'],

            // Women — Dresses
            ['Wrap Midi Dress — Marigold', 'Dresses', 'Nokshi Atelier', 3900, 4500, ['Marigold', 'Black'], $sizesStd, 'Viscose', 'women',
                ['1612722432474-b971cdcea546', '1614093302611-8efc4de12964', '1515886657613-9f3515b0c78f'],
                'A true wrap in fluid viscose with a flutter sleeve. Ties at the waist so it fits the way you want it to.',
                ['is_featured' => true, 'is_trending' => true], 'Eid Edit 2026'],
            ['Linen Shirt Dress — Ivory', 'Dresses', 'Nokshi Basics', 3400, null, ['Ivory', 'Sage', 'Terracotta'], $sizesStd, 'Linen', 'women',
                ['1591369822096-ffd140ec948f', '1609357605129-26f69add5d6e'],
                'Oversized shirt dress in stonewashed linen. Wear it belted, open over trousers, or as-is.',
                ['is_new' => true], 'Monsoon Linen'],
            ['Tiered Cotton Maxi — Indigo Dot', 'Dresses', 'Nokshi Atelier', 4200, 5200, ['Indigo'], $sizesStd, 'Cotton', 'women',
                ['1609357605129-26f69add5d6e', '1595777457583-95e059d581b8'],
                'Three tiers of hand-dotted indigo cotton. Deep pockets, square neck, endless twirl.',
                [], null],

            // Women — Tops
            ['Puff-Sleeve Blouse — Blush', 'Tops & Blouses', 'Nokshi Atelier', 1650, null, ['Blush', 'Ivory', 'Black'], $sizesStd, 'Cotton', 'women',
                ['1581044777550-4cfa60707c03', '1566174053879-31528523f8ae'],
                'Poplin blouse with a gathered puff sleeve and mother-of-pearl buttons.',
                ['is_trending' => true], 'Everyday Uniform'],
            ['Ribbed Knit Top — Teal', 'Tops & Blouses', 'Nokshi Basics', 1250, 1500, ['Teal', 'Charcoal', 'Marigold'], $sizesStd, 'Cotton', 'women',
                ['1566174053879-31528523f8ae', '1601762603339-fd61e28b698a'],
                'Fitted ribbed knit with a square neckline. Stretchy, soft, and layers under everything.',
                [], 'Everyday Uniform'],

            // Men — Panjabi
            ['Eid Panjabi — Ivory Khadi', 'Panjabi', 'Nokshi Heritage', 3800, 4500, ['Ivory', 'Sky'], ['M', 'L', 'XL', 'XXL'], 'Khadi', 'men',
                ['1609709295948-17d77cb2a69b', '1588359348347-9bc6cbbb689e'],
                'Hand-spun khadi with a band collar and hidden placket. Tonal embroidery at the cuffs.',
                ['is_featured' => true, 'is_new' => true, 'badge' => 'Eid Special', 'badge_color' => '#5B1B4F'], 'Eid Edit 2026'],
            ['Slim Panjabi — Charcoal Linen', 'Panjabi', 'Nokshi Atelier', 3200, null, ['Charcoal', 'Indigo'], ['M', 'L', 'XL', 'XXL'], 'Linen', 'men',
                ['1600950207944-0d63e8edbc3f', '1617137968427-85924c800a22'],
                'A modern slim silhouette in breathable linen. Pair with the matching pajama or with jeans.',
                ['is_trending' => true], 'Monsoon Linen'],
            ['Printed Panjabi — Terracotta Paisley', 'Panjabi', 'Nokshi Atelier', 2900, 3600, ['Terracotta', 'Teal'], ['M', 'L', 'XL', 'XXL'], 'Cotton', 'men',
                ['1626497764746-6dc36546b388', '1609709295948-17d77cb2a69b'],
                'Screen-printed paisley on soft cotton with a contrast inner collar.',
                [], 'Eid Edit 2026'],

            // Men — Shirts
            ['Oxford Shirt — Sky', 'Shirts', 'Nokshi Basics', 2200, null, ['Sky', 'Ivory', 'Sage'], $sizesStd, 'Cotton', 'men',
                ['1588359348347-9bc6cbbb689e', '1598033129183-c4f50c736f10'],
                'Heavyweight Oxford cloth, button-down collar, single chest pocket. The shirt that gets better with every wash.',
                ['is_featured' => true], 'Everyday Uniform'],
            ['Cuban Collar Shirt — Sage Linen', 'Shirts', 'Nokshi Street', 2400, 2900, ['Sage', 'Terracotta', 'Black'], $sizesStd, 'Linen', 'men',
                ['1596755094514-f87e34085b2c', '1558171813-4c088753af8f'],
                'Relaxed Cuban collar in yarn-dyed linen. Made for rooftops and load-shedding evenings.',
                ['is_new' => true, 'is_trending' => true], 'Monsoon Linen'],

            // Men — T-Shirts
            ['Heavyweight Tee — Black', 'T-Shirts', 'Nokshi Street', 950, 1200, ['Black', 'Ivory', 'Charcoal', 'Marigold'], $sizesStd, 'Cotton', 'men',
                ['1583743814966-8936f5b7be1a', '1618354691373-d851c5c3a990', '1503341504253-dff4815485f1'],
                '240 GSM combed cotton with a boxy fit and dropped shoulder. Pre-shrunk. Garment-dyed in Narayanganj.',
                ['is_featured' => true, 'is_trending' => true], 'Everyday Uniform'],
            ['Nokshi Script Tee — Ivory', 'T-Shirts', 'Nokshi Street', 1100, null, ['Ivory', 'Plum'], $sizesStd, 'Cotton', 'unisex',
                ['1521572163474-6864f9cf17ab', '1571945153237-4929e783af4a'],
                'Our Bangla script logo puff-printed across the chest. Unisex, relaxed fit.',
                ['is_new' => true, 'badge' => 'Unisex', 'badge_color' => '#0F766E'], 'Everyday Uniform'],
            ['Pocket Tee — Terracotta', 'T-Shirts', 'Nokshi Basics', 850, null, ['Terracotta', 'Sage', 'Sky'], $sizesStd, 'Cotton', 'men',
                ['1576566588028-4147f3842f27', '1562157873-818bc0726f68'],
                'Slub cotton with a single chest pocket. Slightly slimmer than our heavyweight tee.',
                [], 'Everyday Uniform'],

            // Men — Denim
            ['Straight Jeans — Raw Indigo', 'Denim & Trousers', 'Nokshi Street', 3200, 3800, ['Indigo', 'Black'], ['30', '32', '34', '36'], 'Denim', 'men',
                ['1542272604-787c3835535d', '1604176354204-9268737828e4'],
                '13oz raw selvedge denim in a straight leg. Break them in and make them yours.',
                ['is_featured' => true], 'Everyday Uniform'],
            ['Pleated Trousers — Charcoal', 'Denim & Trousers', 'Nokshi Atelier', 2800, null, ['Charcoal', 'Sage'], ['30', '32', '34', '36'], 'Viscose', 'men',
                ['1594633312681-425c7b97ccd1', '1541099649105-f69ad21f3246'],
                'Double-pleated, tapered trousers in a fluid drape. Dress up or down.',
                ['is_new' => true], 'Everyday Uniform'],

            // Men — Jackets
            ['Chore Jacket — Indigo Canvas', 'Jackets', 'Nokshi Street', 4500, 5500, ['Indigo', 'Terracotta'], $sizesStd, 'Cotton', 'men',
                ['1552374196-1ab2a1c593e8', '1591047139829-d91aecb6caea', '1612423284934-2850a4ea6b0f'],
                'Three-pocket chore coat in washed cotton canvas. Corozo buttons, reinforced seams.',
                ['is_featured' => true, 'is_trending' => true, 'badge' => 'Bestseller', 'badge_color' => '#C2410C'], 'Everyday Uniform'],
            ['Quilted Vest — Black', 'Jackets', 'Nokshi Street', 3600, null, ['Black', 'Sage'], $sizesStd, 'Cotton', 'unisex',
                ['1548126032-079a0fb0099d', '1551028719-00167b16eac5'],
                'Lightweight quilted vest for those two weeks of winter Dhaka gets.',
                [], null],

            // Kids
            ['Girls Frock — Marigold Floral', 'Girls', 'Nokshi Atelier', 1450, 1800, ['Marigold', 'Blush'], ['2-3Y', '4-5Y', '6-7Y', '8-9Y'], 'Cotton', 'kids',
                ['1503919545889-aef636e10ad4', '1622290291468-a28f7a7dc6a8'],
                'Twirl-approved cotton frock with a hand-printed floral and a big bow at the back.',
                ['is_new' => true], 'Eid Edit 2026'],
            ['Boys Panjabi Set — Sky', 'Boys', 'Nokshi Heritage', 1650, null, ['Sky', 'Ivory'], ['2-3Y', '4-5Y', '6-7Y', '8-9Y'], 'Cotton', 'kids',
                ['1519238263530-99bdd11df2ea', '1471286174890-9c112ffca5b4'],
                'Mini panjabi with matching pajama. Soft cotton so they actually keep it on.',
                ['is_featured' => true], 'Eid Edit 2026'],

            // Accessories
            ['Hand-Embroidered Orna — Plum', 'Scarves & Ornas', 'Nokshi Heritage', 1250, null, ['Plum', 'Marigold', 'Teal'], ['Free Size'], 'Cotton', 'women',
                ['1601924994987-69e26d50dc26', '1606760227091-3dd870d97f1d'],
                'Featherweight cotton orna with kantha-stitch edging by artisans in Jessore.',
                ['is_trending' => true], 'Eid Edit 2026'],
            ['Jute Tote — Natural', 'Bags', 'Nokshi Basics', 890, 1100, ['Ivory'], ['Free Size'], 'Cotton', 'unisex',
                ['1590874103328-eac38a683ce7', '1445205170230-053b83016050'],
                'Sturdy jute tote with cotton handles and an inner zip pocket. Carries groceries, books, and moods.',
                ['badge' => 'Eco', 'badge_color' => '#0F766E'], 'Everyday Uniform'],
        ];

        $reviewers = User::role('customer')->get();
        $reviewTexts = [
            [5, 'Exactly as pictured', 'Fabric quality is far better than I expected at this price. Fit is true to size.'],
            [4, 'Lovely, slightly long', 'Beautiful colour. Had to get it altered a bit but the material is gorgeous.'],
            [5, 'Got so many compliments', 'Wore it to a wedding and everyone asked where it was from. Ordering another colour.'],
            [4, 'Great everyday piece', 'Comfortable and washes well. Delivery inside Dhaka took two days.'],
            [5, 'Worth every taka', 'The stitching and finishing are top notch. Packaging was also lovely.'],
        ];

        foreach ($products as $i => [$name, $catName, $brandName, $price, $compare, $colorNames, $sizes, $fabricName, $gender, $imgs, $desc, $flags, $collName]) {
            $product = Product::firstOrCreate(['slug' => str($name)->slug()], array_merge([
                'name' => $name,
                'category_id' => $cats[$catName]->id,
                'brand_id' => $brands[$brandName]->id,
                'price' => $price,
                'compare_price' => $compare,
                'cost_price' => round($price * 0.55),
                'short_description' => $desc,
                'description' => "<p>{$desc}</p><p>Every NOKSHI piece is cut and sewn in our Dhaka workshop in small batches. We use natural fibres wherever possible and finish each garment by hand.</p><ul><li>Fabric: {$fabricName}</li><li>Made in Bangladesh</li><li>Ships in 1–2 business days</li></ul>",
                'material' => $fabricName,
                'care_instructions' => 'Gentle machine wash cold with like colours. Do not bleach. Dry in shade. Iron on medium heat.',
                'gender' => $gender,
                'has_variants' => true,
                'is_active' => true,
                'published_at' => now()->subDays(60 - $i),
                'views' => rand(120, 4200),
                'sold_count' => rand(4, 180),
                'tags' => Arr::random(['new season', 'handmade', 'bestseller', 'natural fibre', 'festive', 'daily'], 3),
            ], $flags));

            if ($product->wasRecentlyCreated) {
                foreach ($imgs as $k => $id) {
                    $product->images()->create([
                        'path' => $this->u($id),
                        'alt' => $name,
                        'is_primary' => $k === 0,
                        'sort_order' => $k,
                    ]);
                }

                $total = 0;
                foreach ($colorNames as $cn) {
                    foreach ($sizes as $sz) {
                        $stock = rand(0, 14);
                        $total += $stock;
                        $product->variants()->create([
                            'sku' => strtoupper(str($product->sku.'-'.substr($cn, 0, 3).'-'.$sz)->slug()),
                            'size' => $sz,
                            'color' => $cn,
                            'color_code' => $colors[$cn] ?? null,
                            'stock' => $stock,
                        ]);
                    }
                }
                $product->update(['stock' => $total]);

                $attrIds = collect();
                foreach ($sizes as $sz) {
                    if ($v = $size->values()->where('value', $sz)->first()) {
                        $attrIds->push($v->id);
                    }
                }
                foreach ($colorNames as $cn) {
                    if ($v = $color->values()->where('value', $cn)->first()) {
                        $attrIds->push($v->id);
                    }
                }
                if ($v = $fabric->values()->where('value', $fabricName)->first()) {
                    $attrIds->push($v->id);
                }
                $product->attributeValues()->sync($attrIds->all());

                if ($collName && isset($coll[$collName])) {
                    $product->collections()->attach($coll[$collName]->id, ['sort_order' => $i]);
                }

                foreach (Arr::random($reviewTexts, rand(1, 3)) as [$rating, $title, $body]) {
                    Review::create([
                        'product_id' => $product->id,
                        'user_id' => $reviewers->random()?->id,
                        'rating' => $rating,
                        'title' => $title,
                        'body' => $body,
                        'is_verified' => true,
                        'status' => 'approved',
                        'created_at' => now()->subDays(rand(2, 40)),
                    ]);
                }
            }
        }

        // Related products: same category siblings.
        Product::with('category')->get()->each(function (Product $p) {
            $related = Product::where('category_id', $p->category_id)->where('id', '!=', $p->id)->inRandomOrder()->limit(4)->pluck('id');
            $p->related()->sync($related);
        });
    }
}
