<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StoreController extends Controller
{
    public function home(): View
    {
        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->take(6)
            ->get();

        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('store.home', compact('categories', 'posts'));
    }

    public function shop(Request $request): View
    {
        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $query = Product::query()
            ->active()
            ->with(['images', 'category']);

        $this->applyFilters($query, $request);

        $sort = $request->input('sort', 'default');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'newest' => $query->latest(),
            default => $query->orderBy('id', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        $attributes = ProductAttribute::query()
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return view('store.shop', [
            'products' => $products,
            'attributes' => $attributes,
            'categories' => $categories,
            'category' => null,
            'title' => 'Каталог',
        ]);
    }

    public function category(string $slug): View
    {
        $category = Category::query()
            ->active()
            ->where('slug', $slug)
            ->with('children')
            ->firstOrFail();

        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $subcategories = $category->children()->active()->orderBy('sort_order')->get();

        $categoryIds = collect([$category->id]);
        $childIds = $subcategories->pluck('id');
        $categoryIds = $categoryIds->merge($childIds);

        $query = Product::query()
            ->active()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->with(['images', 'category']);

        $this->applyFilters($query, request());

        $sort = request()->input('sort', 'default');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'newest' => $query->latest(),
            default => $query->orderBy('id', 'desc'),
        };

        $products = $query->paginate(12)->withQueryString();

        $attributes = ProductAttribute::query()
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return view('store.shop', [
            'products' => $products,
            'attributes' => $attributes,
            'categories' => $categories,
            'subcategories' => $subcategories,
            'category' => $category,
            'title' => $category->name,
        ]);
    }

    public function product(string $slug): View
    {
        $product = Product::query()
            ->active()
            ->where('slug', $slug)
            ->with([
                'images',
                'category',
                'specifications',
                'variants' => fn ($q) => $q->active(),
                'variants.attributeValues.attribute',
            ])
            ->firstOrFail();

        $related = Product::query()
            ->active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->take(4)
            ->get();

        return view('store.product', compact('product', 'related'));
    }

    public function blogIndex(): View
    {
        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('store.blog-index', compact('posts'));
    }

    public function blogShow(string $slug): View
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedPosts = Post::query()
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('store.blog-show', compact('post', 'relatedPosts'));
    }

    public function contacts(): View
    {
        return view('store.contacts');
    }

    public function staticPage(string $slug): View
    {
        $page = Page::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('store.page', compact('page'));
    }

    public function search(Request $request): View
    {
        $searchQuery = $request->input('q', '');

        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        $query = Product::query()
            ->active()
            ->with(['images', 'category']);

        if ($searchQuery) {
            $variants = self::caseVariants($searchQuery);
            $query->where(function ($q) use ($variants) {
                foreach ($variants as $v) {
                    $q->orWhere('name', 'like', "%{$v}%")
                        ->orWhere('sku', 'like', "%{$v}%")
                        ->orWhere('description', 'like', "%{$v}%")
                        ->orWhere('short_description', 'like', "%{$v}%");
                }
            });
        }

        $products = $query->paginate(12)->withQueryString();

        $attributes = ProductAttribute::query()
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return view('store.shop', [
            'products' => $products,
            'attributes' => $attributes,
            'categories' => $categories,
            'category' => null,
            'searchQuery' => $searchQuery,
            'title' => $searchQuery ? "Поиск: {$searchQuery}" : 'Поиск',
        ]);
    }

    public function searchAjax(Request $request): JsonResponse
    {
        $q = $request->input('q', '');

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $variants = self::caseVariants($q);

        $products = Product::query()
            ->active()
            ->where(function ($query) use ($variants) {
                foreach ($variants as $v) {
                    $query->orWhere('name', 'like', "%{$v}%")
                        ->orWhere('sku', 'like', "%{$v}%");
                }
            })
            ->with('images')
            ->take(8)
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'sku' => $p->sku,
                'price' => number_format($p->price, 0, '', ' ') . ' ₽',
                'url' => route('shop.product', $p->slug),
                'image' => $p->primaryImagePath(),
            ]);

        return response()->json($products);
    }

    private function applyFilters($query, Request $request): void
    {
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->input('price_max'));
        }

        if ($request->filled('color')) {
            $colorValues = (array) $request->input('color');
            $query->whereHas('variants.attributeValues', function ($q) use ($colorValues) {
                $q->whereIn('product_attribute_values.id', $colorValues)
                    ->whereHas('attribute', fn ($a) => $a->where('slug', 'color'));
            });
        }

        if ($request->filled('size')) {
            $sizeValues = (array) $request->input('size');
            $query->whereHas('variants.attributeValues', function ($q) use ($sizeValues) {
                $q->whereIn('product_attribute_values.id', $sizeValues)
                    ->whereHas('attribute', fn ($a) => $a->where('slug', 'razmer'));
            });
        }
    }

    /**
     * Generate case variants for SQLite Cyrillic-safe LIKE search.
     * SQLite LOWER()/UPPER() only handles ASCII, so we search multiple variants.
     */
    private static function caseVariants(string $q): array
    {
        return array_unique([
            $q,
            mb_strtolower($q),
            mb_strtoupper(mb_substr($q, 0, 1)) . mb_substr(mb_strtolower($q), 1),
            mb_strtoupper($q),
        ]);
    }
}
