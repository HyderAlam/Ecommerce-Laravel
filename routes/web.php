<?php

use Illuminate\Support\Str;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\ProductController;
use App\Http\Controllers\admin\CategoryController;
use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\adminloginController;
use App\Http\Controllers\admin\TempImagesController;
use App\Http\Controllers\admin\SubCategoryController;
use App\Http\Controllers\admin\ProductImageController;
use App\Http\Controllers\admin\ProductSubCategoryController;




// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/',[FrontController::class,'index'])->name('front.home');

Route::get('/shop/{categorySlug?}/{subCategorySlug?}',[ShopController::class,'index'])->name('front.shop');

Route::get('/product/{slug}',[ShopController::class,'product'])->name('front.product');

Route::get('/cart',[CartController::class,'cart'])->name('front.cart');
Route::post('/add-to-cart',[CartController::class,'addToCart'])->name('front.addToCart');
Route::post('/update-cart',[CartController::class,'updateCart'])->name('front.updateCart');
Route::post('/delete-item',[CartController::class,'deleteItem'])->name('front.deleteItem');





Route::group(['prefix' => 'admin'],function(){

    Route::group(['middleware' => 'admin.guest'],function(){

        Route::get('/login-page',[adminLoginController::class,'index'])->name('admin.login');
        Route::post('/authenticate',[adminLoginController::class,'authenticate'])->name('admin.authenticate');

    });

    Route::group(['middleware' => 'admin.auth'],function(){

        Route::get('/dashboard',[DashboardController::class,'index'])->name('admin.dashboard');
        Route::get('/logout',[DashboardController::class,'logout'])->name('admin.logout');

        //Category Routes
        Route::get('/categories/create/',[CategoryController::class,'create'])->name('categories.create');
        Route::post('/categories/store/',[CategoryController::class,'store'])->name('categories.store');
        Route::get('/categories',[CategoryController::class,'index'])->name('categories.index');
        Route::get('/categories/{category}/edit',[CategoryController::class,'edit'])->name('categories.edit');
        Route::put('/categories/{category}',[CategoryController::class,'update'])->name('categories.update');
        Route::delete('/categories/{category}',[CategoryController::class,'destory'])->name('categories.destroy');
       
        //sub categoy routes
        Route::get('/sub-category/create/',[SubCategoryController::class,'create'])->name('subcategory.create');
        Route::post('/sub-category/store/',[SubCategoryController::class,'store'])->name('subcategory.store');
        Route::get('/sub-category',[SubCategoryController::class,'index'])->name('subcategory.index');
        Route::get('/sub-category/{subCategory}/edit',[SubCategoryController::class,'edit'])->name('subcategory.edit');
        Route::put('/subcategory/{id}', [SubCategoryController::class, 'update'])->name('subcategory.update');
        Route::delete('/subcategory/{id}',[SubCategoryController::class,'destroy'])->name('subcategory.destroy');

        //Brands Route
        Route::get('/brands/create/',[BrandController::class,'create'])->name('brand.create');
        Route::post('/brands/store',[BrandController::class,'store'])->name('brand.store');
        Route::get('/brands',[BrandController::class,'index'])->name('brand.index');
        Route::get('/brands/{brand}/edit',[BrandController::class,'edit'])->name('brand.edit');
        Route::put('/brands/{brand}/update',[BrandController::class,'update'])->name('brand.update');
        Route::delete('/brands/{brand}/destroy',[BrandController::class,'destroy'])->name('brand.destroy');

        //Products Route
        Route::get('/products/create',[ProductController::class,'create'])->name('products.create');
        Route::post('/products/store',[ProductController::class,'store'])->name('products.store');
        Route::get('/products',[ProductController::class,'index'])->name('products.index');
        Route::get('/products/{product}/edit',[ProductController::class,'edit'])->name('products.edit');
        Route::put('/products/{product}',[ProductController::class,'update'])->name('products.update');
        Route::delete('/products/{product}/destroy',[ProductController::class,'destroy'])->name('products.destroy');

        Route::get('/get-products',[ProductController::class,'getProducts'])->name('products.getProducts');

        //ProductImage Controller
        Route::post('/product-images/update',[ProductImageController::class,'update'])->name('product-images.update');
        Route::delete('/product-images',[ProductImageController::class,'destroy'])->name('product-images.destroy');


        //product Sub categories for ajax
        Route::get('/product-subcategories',[ProductSubCategoryController::class,'index'])->name('product-subcategories.index');


       //temp-images-create
        Route::post('/upload-temp-image',[TempImagesController::class,'create'])->name('temp-images.create');
        

        Route::get('/getSlug',function(Request $req){
            $slug = '' ;
            if(!empty($req->title)) {
                $slug = Str::slug($req->title);
            }

            return response()->json([
                'status' => true,
                'slug' => $slug,
            ]);

        })->name('getSlug');

    });


});



