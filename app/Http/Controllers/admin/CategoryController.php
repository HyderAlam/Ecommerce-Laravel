<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\TempImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Laravel\Facades\Image;


class CategoryController extends Controller
{
    public function index(Request $req) {
        if(Auth::guard('admin')->check()){

        if (!empty($req->get('keyword'))) {
            $categorys = Category::orderBy('id', 'DESC')
                                 ->where('name', 'like', '%'.$req->keyword.'%')
                                 ->paginate(10);
        } else {
          
            $categorys = Category::orderBy('id', 'DESC')->paginate(10);
        }
    
        // Return the view with the results
        return view('admin.category.list', compact('categorys'));
    } else {

        return abort(403);
    }

}

    public function create() {
    
     if(Auth::guard('admin')->check()){

        return view('admin.category.create');
    
    } else {
            return abort(403);
    }

    }

   
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $category = new Category();
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            // Handle image upload
            if (!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '.' . $ext;
                $sourcePath = public_path('temp/' . $tempImage->name);
                $destinationPath = public_path('uploads/category/' . $newImageName);
                File::copy($sourcePath, $destinationPath);

                //generate Image thumbnail
                
                //destination_path
                $dPath = public_path('uploads/category/thumb/' . $newImageName);

                $img=Image::read($sourcePath);
                $img->cover(450,600);
                $img->save($dPath);
                
                $category->image = $newImageName;
                $category->save();
            }
           
            $request->session()->flash('success','Category Added Successfully ');
            return response()->json([
                'status' => true,
                'success' => 'Category Added Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }


    public function edit(category $category) {

        $category = Category::findOrFail($category->id);
        if(empty($category)){
            return redirect()->route('categories.index');
        } 

        return view('admin.category.edit',compact('category'));
    }

    
    public function update(category $category ,Request $request) {

        $category = Category::findOrFail($category->id);
        if(empty($category)){
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Category Not Found',
            ]);
    
            $request->session()->flash('error','Category Not Found Successfully ');
        } 

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$category->slug.',slug',
            'status' => 'required',
        ]);

        if ($validator->passes()) {
            $category->name = $request->name;
            $category->slug = $request->slug;
            $category->status = $request->status;
            $category->showHome = $request->showHome;
            $category->save();

            $oldimage = $category->image;

            // Handle image upload
            if (!empty($request->image_id)){
                $tempImage = TempImage::find($request->image_id);
                $extArray = explode('.',$tempImage->name);
                $ext = last($extArray);
                $newImageName = $category->id . '-' .time()  . '.' . $ext;
                $sourcePath = public_path('temp/' . $tempImage->name);
                $destinationPath = public_path('uploads/category/' . $newImageName);
                File::copy($sourcePath, $destinationPath);
                $category->image = $newImageName;
                $category->save();
                File::delete(public_path().'/uploads/category/'.$oldimage);
            }
    
            $request->session()->flash('success','Category Updated Successfully ');
            return response()->json([
                'status' => true,
                'success' => 'Category Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }

        
    }

    public function destory(category $category, Request $request) {
        $category = Category::findOrFail($category->id);
       
        $pathImg=public_path().'/uploads/category/'.$category->image;
        $pathThumb=public_path().'/uploads/category/thumb/'.$category->image;
        
            if(File::exists($pathImg)) {
            
                File::delete(public_path().'/uploads/category/'.$category->image);
            
            }

            if(File::exists($pathThumb)) {
            
                File::delete(public_path().'/uploads/category/thumb/'.$category->image);
            
            }

                $category->delete();
                $request->session()->flash('success','Category Deleted Successfully ');
                return response()->json([
                    'status' => true,
                    'success' => 'Category Deleted Successfully',
                ]);

    }
}
