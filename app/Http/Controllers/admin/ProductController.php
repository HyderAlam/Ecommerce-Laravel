<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\TempImage;
use App\Models\SubCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;


class ProductController extends Controller
{
    public function index(Request $request){
        
        $products = Product::with('productImages')->orderBy('id','desc');
        
        if(!empty($request->get('keyword'))) {
            $products  = $products->where('name','like','%'.$request->keyword. '%');
        }

        $products = $products->paginate(10);


        return view('admin.products.list',compact('products'));
        
    }

    public function create() {

        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        return view('admin.products.create',['categories' => $categories, 'brands' => $brands]);
    
    }

    public function store(Request $request) {
    
        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku',
            'track_qty' => 'required|in:yes,no',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:yes,no',
        ];

            if (!empty($request->track_qty) && $request->track_qty == 'yes' ) {
                $rules['qty'] = 'required|numeric';
            }


      $validator =  Validator::make($request->all(),$rules);

            if($validator->passes()){
                
                $product = new Product;
                $product->name = $request->title;
                $product->slug = $request->slug;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->compare_price = $request->compare_price;
                $product->sku = $request->sku;
                $product->barcode = $request->barcode;
                $product->track_qty = $request->track_qty;
                $product->qty = $request->qty;
                $product->status = $request->status;
                $product->category_id = $request->category;                                
                $product->sub_category_id = $request->sub_category;
                $product->brand_id = $request->brand;
                $product->is_featured = $request->is_featured;
                $product->shipping_returns = $request->shipping_returns;
                $product->short_description = $request->short_description;
                $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';
               
                $product->save();

                //save gallery pics

                if(!empty($request->image_array)) {
                    foreach($request->image_array as $temp_image_id) {

                        $tempImageInfo = TempImage::find($temp_image_id);
                        //for extention
                        $extArray = explode('.',$tempImageInfo->name);
                        $ext = last($extArray);

                        $productImage =new ProductImage();
                        $productImage->product_id = $product->id;
                        $productImage->image = 'Null';
                        $productImage->save();

                        $imageName = $product->id. "-" . $productImage->id . "-" .time(). "." .$ext;
                        //4-1-45646.jpg
                        $productImage->image = $imageName;
                        $productImage->save();

                        //Generate Product Thumbnails

                        //large image
                        $sPath = public_path().'/temp/'.$tempImageInfo->name;
                        $dPath = public_path().'/uploads/product/large/'.$imageName;
                        $image = Image::read($sPath);
                        $image->resize(1400,null,function($constraint){
                            $constraint->aspectRatio();
                        });
                        $image->save($dPath);

                        //small image

                        $dPath = public_path().'/uploads/product/small/'.$imageName;
                        $image = Image::read($sPath);
                        $image->cover(300,300);
                        $image->save($dPath);


                    }
                }
                
                $request->session()->flash('success','Product Added Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Product Added Successfully',     
                ]);

            } else {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),     
                ]);
            }

    }

    public function edit(int $id , Request $request) {

        $product = Product::find($id);
         if(empty($product)) {
            return redirect()->route('products.index')->with('error','Product Not Found');
         }
         $productImage = ProductImage::where('product_id',$product->id)->get();

        $subCategory = SubCategory::where('category_id',$product->category_id)->get();
        //   dd($subCategory);
            $categories = Category::orderBy('name', 'ASC')->get();
            $brands = Brand::orderBy('name', 'ASC')->get();

            $related_products = [];
            //fetch related products
            if($product->related_products != '') {
                $productArray = explode(',',$product->related_products);
                $related_products=Product::whereIn('id',$productArray)->get();
             }

        return view('admin.products.edit',['categories' => $categories, 'brands' => $brands , 'product' => $product, 'subCategory' => $subCategory , 'productImage' => $productImage , 'related_products' => $related_products]);
    }

    public function update(int $id , Request $request) {
       
        $product =Product::find($id);

        $rules = [
            'title' => 'required',
            'slug' => 'required|unique:products,slug,'.$product->id.',id',
            'price' => 'required|numeric',
            'sku' => 'required|unique:products,sku,'.$product->id.',id',
            'track_qty' => 'required|in:yes,no',
            'category' => 'required|numeric',
            'is_featured' => 'required|in:yes,no',
        ];

            if (!empty($request->track_qty) && $request->track_qty == 'yes' ) {
                $rules['qty'] = 'required|numeric';
            }


      $validator =  Validator::make($request->all(),$rules);

            if($validator->passes()){
                
                $product->name = $request->title;
                $product->slug = $request->slug;
                $product->description = $request->description;
                $product->price = $request->price;
                $product->compare_price = $request->compare_price;
                $product->sku = $request->sku;
                $product->barcode = $request->barcode;
                $product->track_qty = $request->track_qty;
                $product->qty = $request->qty;
                $product->status = $request->status;
                $product->category_id = $request->category;                                
                $product->sub_category_id = $request->sub_category;
                $product->brand_id = $request->brand;
                $product->is_featured = $request->is_featured;
                $product->shipping_returns = $request->shipping_returns;
                $product->short_description = $request->short_description;
                $product->related_products = (!empty($request->related_products)) ? implode(',',$request->related_products) : '';


                $product->save();

                $request->session()->flash('success','Product Updated Successfully');
                return response()->json([
                    'status' => true,
                    'message' => 'Product Updated Successfully',     
                ]);

            } else {

                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),     
                ]);
            }
    }

    public function destroy(int $id , Request $request) {
      
        $product = Product::find($id);

        if (empty($product)) {

            $request->session()->flash('error','Product Not Found');

            return response()->json([
                'status' => false,
                'notFound' => true,     
            ]);
        }

        
        $productImage = ProductImage::where('product_id',$id)->get();
       
                if(!empty($productImage)) {
                    
                    foreach($productImage as $image) {
                            
                        $largePath= public_path('uploads/product/large/'.$image->image);
                        $smallPath= public_path('uploads/product/small/'.$image->image);

                            if(File::exists($largePath)) {
                                 File::delete($largePath);
                            }

                            if(File::exists($smallPath)) {
                                 File::delete($smallPath);
                            }
                            
                    }

                    ProductImage::where('product_id',$id)->delete();

                }
                $product->delete();

                $request->session()->flash('success','Product Deleted Successfully');
                
                return response()->json([
                    'status' => true,
                    'errors' => 'Product Deleted Successfully',     
                ]);

    }


        public function getProducts(Request $request) {
            $tempProduct = [];
            if($request->term != "") {
                $products = Product::where('name','like','%'.$request->term.'%')->get();

                if ($products != null) {
                    foreach($products as $product) {
                        $tempProduct[] = array('id' => $product->id, 'text' => $product->name);
                    }
                }
            }

                return response()->json([
                    'tags' => $tempProduct,
                    'status' => true,
                ]);

        }
}
