<?php

namespace App\Http\Controllers\admin;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductSubCategoryController extends Controller
{
    public function index(Request $request) {
        
        if(!empty($request->category_id)) {

            $subcategory = SubCategory::where('category_id',$request->category_id)
                ->orderBy('name','ASC')
                ->get();

            return response()->json([
                    'status' => true,
                    'subcategories' => $subcategory,
            ]);

        } else {

            return response()->json([
                'status' => true,
                'subcategories' => [],
            ]);

        }    
       
    }



 }
