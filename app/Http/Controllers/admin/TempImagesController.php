<?php

namespace App\Http\Controllers\admin;

use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Laravel\Facades\Image;


class TempImagesController extends Controller
{
      public function create(Request $req)
      {
        $image = $req->image;
        
        if (!empty($image)) {
          $ext = $image->getClientOriginalExtension();
          $newname = time() . '.' . $ext;
          $temp = new TempImage();
          $temp->name = $newname;
          $temp->save();
          $image->move(public_path() . '/temp', $newname);
      
          // Generate thumbnail
          $sPass = public_path() . '/temp/' . $newname; // Corrected path concatenation
          $image = Image::read($sPass); // Correctly create the image instance
          $dPass = public_path() . '/temp/thumb/' . $newname; // Corrected path concatenation
          //$image->resize(300, 200);
          $image->cover(300, 250);
          $image->save($dPass);

            return response()->json([
                'status' => true,
                'image_id' => $temp->id,
                'image_path' => asset('/temp/thumb/'.$newname),
                'message' => 'image uploaded successfully',
            ]);
           
        }


      }
}
