<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductFilterService $filterService) {}

    public function index(Request $request)
    {
        $baseQuery = Product::query()->active()->with(['media', 'categories', 'brand']);

        // If a category is selected, get its filter attributes
        $filterAttributes = [];
        $priceRange = ['min' => 0, 'max' => 0];
        $selectedCategory = null;

        if ($request->filled('category')) {
            $selectedCategory = Category::where('slug', $request->category)->first();
            if ($selectedCategory) {
                $categoryIds = $selectedCategory->descendants()->pluck('id')
                    ->push($selectedCategory->id)->toArray();

                // Build a base query for counting (products in this category, active, without attr filters)
                $countQuery = Product::query()->active()
                    ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));

                $filterAttributes = $this->filterService->getFilterData($categoryIds, $countQuery);
                $priceRange = $this->filterService->getPriceRange($countQuery);
            }
        }

        if (empty($priceRange['max'])) {
            $priceRange = $this->filterService->getPriceRange(Product::query()->active());
        }

        $products = $this->filterService->apply($baseQuery, $request)
            ->paginate(12)->withQueryString();

        $categories = Category::active()->whereNull('parent_id')
            ->with('children')
            ->withCount('products')
            ->orderBy('sort_order')
            ->get();

        $brands = Brand::where('is_active', true)->withCount('products')->orderBy('name')->get();

        return view('products.index', compact(
            'products', 'categories', 'brands', 'filterAttributes', 'priceRange', 'selectedCategory'
        ));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['media', 'categories', 'brand', 'attributeValues.attribute']);
        $product->increment('view_count');

        $relatedProducts = Product::active()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $product->categories->pluck('id')))
            ->where('id', '!=', $product->id)
            ->with(['media', 'brand'])
            ->limit(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function quickView(Product $product)
    {
        abort_unless($product->is_active, 404);
        $product->load(['media', 'brand', 'attributeValues.attribute']);

        return view('products.quick-view', compact('product'));
    }
}
