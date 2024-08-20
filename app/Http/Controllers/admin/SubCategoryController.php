<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    public function create()
    {
        $category = Category::orderBy('name','ASC')->get();       
        return view('admin.subcategory.create',compact('category'));
    }

    public function store(Request $request)
    {
       $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug',
            'categoryid' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()) {

            $subcategory = new SubCategory();
            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->showHome = $request->showHome;
            $subcategory->category_id = $request->categoryid;
            $subcategory->save();

            $request->session()->flash('success','Sub-Category Created Succesfully');
            return response([
                'status' => true,
                'message' => 'Sub-Category Created Succesfully',
            ]); 

        } else {

            return response([
                'status' => false,
                'errors' => $validator->errors(),
            ]); 
        }

    }


    public function index(Request $req) {
        
        if(Auth::guard('admin')->check()){
        
            if (!empty($req->get('keyword'))) {
                $categorys = SubCategory::orderBy('id', 'DESC')
                                     ->where('name', 'like', '%'.$req->keyword.'%')
                                     ->with('categories')
                                     ->paginate(10);
            } else {
              
                $categorys = SubCategory::orderBy('id', 'DESC')
                                ->with('categories')
                                ->paginate(10);
                                
            }
        
            // Return the view with the results
            return view('admin.subcategory.list', compact('categorys'));
        } else {
            return abort(403);
        }
    }


    public function edit(subCategory $subCategory ,Request $req) {
        if(Auth::guard('admin')->check()){
              
        $category = Category::orderBy('id','DESC')->get();
        $subcategory = SubCategory::findOrFail($subCategory->id);
        return view('admin.subcategory.edit',['category' => $category , 'subcategory' => $subcategory ]);
        
        } else {
          return abort(403);
        }  
    } 

    public function update(int $id ,Request $request) {

        $subcategory = SubCategory::findOrFail($id);
  
        if (!$subcategory) {
            return response()->json(['status' => false, 'errors' => ['message' => 'Subcategory not found']], 404);
        }
    
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'slug' => 'required|unique:sub_categories,slug,'.$subcategory->id.',id',
            'categoryid' => 'required',
            'status' => 'required',
        ]);

        if($validator->passes()) {

            $subcategory->name = $request->name;
            $subcategory->slug = $request->slug;
            $subcategory->status = $request->status;
            $subcategory->showHome = $request->showHome;
            $subcategory->category_id = $request->categoryid;
            $subcategory->save();

            $request->session()->flash('success','Sub-Category Update Succesfully');
            return response([
                'status' => true,
                'message' => 'Sub-Category Update Succesfully',
            ]); 

        } else {

            return response([
                'status' => false,
                'errors' => $validator->errors(),
            ]); 
        }

    }

    public function destroy(int $id , Request $request) {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();
        $request->session()->flash('success','Sub-Category Deleted Succesfully');
            
            return response([
                'status' => true,
                'message' => 'Sub-Category Deleted Succesfully',
            ]); 

    }
}
