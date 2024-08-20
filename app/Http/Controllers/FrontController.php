<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index() {
        $product=Product::where('is_featured','yes')
                    ->where('status','1')
                    ->orderBy('id','DESC')
                    ->limit(8) 
                    ->with('productImages')
                    ->get();
        
        $latests = Product::orderBy('id','DESC')
                    ->where('status',1)
                    ->limit(8) 
                    ->get();     
        $data['product'] = $product;
        $data['latests'] = $latests;

        return view('front.home',$data);
    }
    
}
