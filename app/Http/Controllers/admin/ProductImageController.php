<?php

namespace App\Http\Controllers\admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class ProductImageController extends Controller
{
   public function update(Request $request) {

        $image =$request->image;
        $ext = $image->getClientOriginalExtension();
        $sPath  = $image->getPathName();

        $productImage =new ProductImage();
        $productImage->product_id = $request->product_id;
        $productImage->image = 'Null';
        $productImage->save();


        $imageName = $request->product_id. "-" . $productImage->id . "-" .time(). "." .$ext;
        $productImage->image = $imageName;
        $productImage->save();

        //large Image
        
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

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'image_path' => asset('uploads/product/small/'.$productImage->image),
            'message' => 'image updated successfully',
            
        ]);
   }


   public function destroy(Request $request) {
    $productImage = ProductImage::find($request->id);

        if(empty($productImage)) {
            
            return response()->json([
                'status' => false,
                'message' => 'image not found',
            ]);
        }

    //Delete Images From Folder
    File::delete(public_path('uploads/product/large/'.$productImage->image));
    File::delete(public_path('uploads/product/small/'.$productImage->image));
    $productImage->delete();

    return response()->json([
        'status' => true,
        'message' => 'image Delete successfully',
        
    ]);

   }
}
