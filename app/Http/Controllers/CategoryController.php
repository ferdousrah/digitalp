<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use App\Services\ProductFilterService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private ProductFilterService $filterService) {}

    public function index()
    {
        $categories = Category::active()
            ->whereIsRoot()
            ->withCount('products')
            ->with(['children' => fn ($q) => $q->active()->withCount('products'), 'media'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category, Request $request)
    {
        abort_unless($category->is_active, 404);
        $category->load('children', 'media');

        $descendantIds = $category->descendants()->pluck('id')->push($category->id);

        // Base query for products in this category
        $countQuery = Product::query()->active()
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $descendantIds));

        // Get filter attribute data for sidebar
        $filterAttributes = $this->filterService->getFilterData($descendantIds->toArray(), $countQuery);
        $priceRange = $this->filterService->getPriceRange($countQuery);

        $products = $this->filterService->apply(
            Product::query()->active()
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $descendantIds))
                ->with(['media', 'brand']),
            $request
        )->paginate(12)->withQueryString();

        $brands = Brand::where('is_active', true)->withCount('products')->orderBy('name')->get();

        return view('categories.show', compact(
            'category', 'products', 'brands', 'filterAttributes', 'priceRange'
        ));
    }
}
