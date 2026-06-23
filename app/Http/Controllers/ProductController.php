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

        // Filter attributes: scoped to the selected category, or all filterable attributes
        // (used by the products listed) when browsing all products.
        $selectedCategory = $request->filled('category')
            ? Category::where('slug', $request->category)->first()
            : null;

        if ($selectedCategory) {
            $categoryIds = $selectedCategory->descendants()->pluck('id')
                ->push($selectedCategory->id)->toArray();
            $countQuery = Product::query()->active()
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds));
        } else {
            $categoryIds = [];
            $countQuery = Product::query()->active();
        }

        $filterAttributes = $this->filterService->getFilterData($categoryIds, $countQuery);
        $priceRange = $this->filterService->getPriceRange($countQuery);

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
        $product->load(['media', 'categories', 'brand', 'attributeValues.attribute', 'approvedReviews', 'publishedQuestions']);
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
