<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function addToCart(Request $request) {
        
        $product = Product::with('productImages')->find($request->id);

        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
            ]);
        }
        //rowId is genrate Unique
        $cartItem = Cart::search(function ($cartItem, $rowId) use ($product) {
            return $cartItem->id === $product->id;
        })->first();

        if ($cartItem) {
            return response()->json([
                'status' => false,
                'message' =>   $product->name.'already added to the cart',
            ]);
        } else {
            // Add the product to the cart
            Cart::add(
                $product->id,
                $product->name,
                1,
                $product->price,
                ['productImage' => $product->productImages->first() ?? '']
            );

            session()->flash('success',$product->name . ' added to the cart');
            return response()->json([
                'status' => true,
                'message' => $product->name . ' added to the cart',
            ]);
        }
    }

    public function cart() {
        
        $cartContent = Cart::content();
        //dd( $cartContent);
        return view('front.cart', compact('cartContent'));
    }


    public function updateCart(Request $request) {
            //rowId is genrate Unique

            $rowId=$request->rowId;
            $qty =$request->qty;

            //check qty available in stock

            //cart_get is function used to get product info

                   $itemInfo = Cart::get($rowId);
                   $product = Product::find($itemInfo->id);

                   if($product->track_qty == 'yes') {
                    if ($qty  <=  $product->qty) {
                         Cart::update($rowId,  $qty);
                         $message = 'cart Updated successfully';    
                         $status = true;
                         session()->flash('success','cart Updated successfully');
                        } else {
                           $message = 'Request Qty ('.$qty.') not availble in stock';    
                            $status = false;
                            session()->flash('error','Request Qty ('.$qty.') not availble in stock');
                       }   
                   } else {
                    Cart::update($rowId,  $qty);
                    $message = 'cart Updated successfully';    
                    $status = true;
                    session()->flash('success','cart Updated successfully');
                }
            
            return response()->json([
                'status' => $status,
                'message' =>$message ,
            ]);
    }


        public function deleteItem(Request $request) {
            
            $itemInfo = Cart::get($request->rowId);

                if($itemInfo == null) {
                    session()->flash('error','Item Not Found In Cart');
                    return response()->json([
                        'status' => false,
                        'message' => 'Item Not Found In Cart',
                    ]);
                } 

                    Cart::remove($request->rowId);
                    session()->flash('success','Item removed from card successfully');
                    return response()->json([
                        'status' => true,
                        'message' => 'Item removed from card successfully',
                    ]);
           
        }
}
