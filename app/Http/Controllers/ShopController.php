<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request, $categorySlug = null, $subCategorySlug = null) {
            $categorySelected = '';
            $subCategorySelected = '';
            $brandsArray = [];


            
            $categorys = Category::orderBy('name','ASC')
                ->where('status','1')
                ->withWhereHas('subcategories',function($sql){
                    $sql->where('status','1');
                    })->limit(5)
                    ->get();
                    

                $brands = Brand::orderBy('name','ASC')
                        ->where('status',1)
                        ->get();

        
                $products = Product::where('status', 1)->with('productImages');

                // Applying Filter for products
                if (!empty($categorySlug)) {
                    $category = Category::where('slug', $categorySlug)->first();
                    $products = $products->where('category_id', $category->id);
                    $categorySelected = $category->id;
                }
                
                if (!empty($subCategorySlug)) {
                    $subcategory = SubCategory::where('slug', $subCategorySlug)->first();
                    $products = $products->where('sub_category_id', $subcategory->id);
                    $subCategorySelected = $subcategory->id;
                }
                
                if (!empty($request->get('brand'))) {
                    // Explode the brand IDs into an array
                    $brandsArray = explode(',', $request->get('brand'));
                
                    // Ensure each brand ID is an integer
                    $brandsArray = array_map('intval', $brandsArray);
                
                    // Apply the brand filter to the products query
                    $products = $products->whereIn('brand_id', $brandsArray);
                }
                
                if ($request->get('price_max') != '' && $request->get('price_min') != '') {
                    if ($request->get('price_max') == 5000) {
                        $products = $products->whereBetween('price', [intval($request->get('price_min')), 1000000]);
                    } else {
                        $products = $products->whereBetween('price', [intval($request->get('price_min')), intval($request->get('price_max'))]);
                    }
                }
                
                // Apply sorting based on user input
                if ($request->get('sort') != '') {
                    if ($request->get('sort') == 'price_asc') {
                        $products = $products->orderBy('price', 'ASC');
                    } elseif ($request->get('sort') == 'price_desc') {
                        $products = $products->orderBy('price', 'DESC');
                    } elseif ($request->get('sort') == 'latest') {
                        $products = $products->orderBy('id', 'DESC');
                    }
                } else {
                    // Default sorting if no sort parameter is provided
                    $products = $products->orderBy('id', 'DESC');
                }
                
                $products = $products->paginate(4);

                $data['products'] = $products;
                $data['brands'] = $brands;
                $data['categorys'] = $categorys;
                $data['categorySelected'] = $categorySelected;
                $data['subCategorySelected'] = $subCategorySelected;
                $data['brandsArray'] = $brandsArray;
                $data['price_min'] = intval($request->get('price_min'));
                $data['price_max'] = (intval($request->get('price_max'))== 0) ? 5000 : $request->get('price_max') ;
                $data['sort'] = $request->get('sort');

        return view('front.shop',$data);
    }

        
    
    public function product($slug) {

        $product = Product::where('slug',$slug)->with('productImages')->first();
        if ($product == null) {
            abort(404);
        }

        //fetch related_ products
         $related_products =[];
        if($product->related_products != '') {
            $productArray = explode(',',$product->related_products);
            $related_products=Product::whereIn('id',$productArray)->with('productImages')->get();
         }

         
   
   
        $data['product'] = $product;
        $data['related_products'] = $related_products;

        return view('front.product',$data);
    }

}
