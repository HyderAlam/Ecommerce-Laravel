<?php

namespace App\Http\Controllers\admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    public function index(Request $request) {
        $brands = Brand::orderBy('id','Desc')->paginate(10);
        if(!empty($request->get('keyword'))) {
            $brands = Brand::orderBy('id','Desc')->where('name','like' , '%'.$request->keyword.'%')->paginate(10);
        }
        return view('admin.brands.list',compact('brands'));
    }

    public function create() { 
        return view('admin.brands.create');
    }

    public function store(Request $request) { 
      $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'status' => 'required',
        ]);

        if($validator->passes())
        {
                $brand = new Brand();
                $brand->name = $request->name;
                $brand->slug = $request->slug;
                $brand->status = $request->status;
                $brand->save();
                $request->session()->flash('success','Brand Created Successfully ');
                
                return response()->json([
                    'status' => true,
                    'message' => 'Brand Created Successfully',
                ]);

        } else {
            return response()->json([
                'status' => False,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit(int $id, Request $request) {
        $brand = Brand::findOrFail($id);
        return view('admin.brands.edit',compact('brand'));
    }

    public function update(int $id, Request $request) {
        $brand = Brand::findOrFail($id);

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$brand->id.',id',
            'status' => 'required',
        ]);

        if($validator->passes())
        {
                $brand->name = $request->name;
                $brand->slug = $request->slug;
                $brand->status = $request->status;
                $brand->save();
                $request->session()->flash('success','Brand Update Successfully ');
                
                return response()->json([
                    'status' => true,
                    'message' => 'Brand Update Successfully',
                ]);

        } else {
            return response()->json([
                'status' => False,
                'errors' => $validator->errors(),
            ]);
        }
     
    }

    public function destroy(int $id , Request $request) {
        $brand = Brand::findOrFail($id);
        $brand->delete();
        $request->session()->flash('success','Brand Deleted Succesfully');
            
            return response([
                'status' => true,
                'message' => 'Sub-Category Deleted Succesfully',
            ]); 
    }
    
}
