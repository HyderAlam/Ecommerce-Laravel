<?php 

use App\Models\Category;
//We Can used this file any where in our Project
    function getCategories() {
        return  Category::orderBy('name','ASC')
        ->where('showHome','yes')
        ->where('status','1')
        ->withWhereHas('subcategories',function($sql){
            $sql->where('showHome','yes')
            ->where('status','1');
        })->get();
        
    }


?>