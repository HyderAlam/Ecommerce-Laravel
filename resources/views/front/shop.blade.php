@extends('front.layouts.app')


@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="#">Home</a></li>
                <li class="breadcrumb-item active">Shop</li>
            </ol>
        </div>
    </div>
</section>

<section class="section-6 pt-5">
    <div class="container">
        <div class="row">            
            <div class="col-md-3 sidebar">
                <div class="sub-title">
                    <h2>Categories</h3>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <div class="accordion accordion-flush" id="accordionExample">

                            @if ($categorys->isNotEmpty())
                                
                            @foreach ($categorys as $key => $category )
                                
                       

                            <div class="accordion-item">
                                @if ($category->subcategories->isNotEmpty())

                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne-{{$key}}" aria-expanded="false" aria-controls="collapseOne-{{$key}}">
                                        {{$category->name}}
                                    </button>
                                </h2>
                                @else

                                <a href="{{route('front.shop',$category->slug)}}" class="nav-item nav-link" {{($categorySelected == $category->id)? 'text-primary' : '' }}  > {{$category->name}}</a>
                                @endif

                                @if ($category->subcategories->isNotEmpty())
                                <div id="collapseOne-{{$key}}" class="accordion-collapse collapse {{($categorySelected == $category->id)? 'show' : '' }}" aria-labelledby="headingOne" data-bs-parent="#accordionExample" style="">
                                    <div class="accordion-body">
                                        <div class="navbar-nav">
                                            @foreach ($category->subcategories as $subCategory )

                                            <a href="{{route('front.shop',[$category->slug,$subCategory->slug])}}" class="nav-item nav-link {{($subCategorySelected == $subCategory->id)? 'text-primary' : '' }} ">{{$subCategory->name}}</a>

                                            @endforeach
                                                
                                          
                                                                                      
                                        </div>
                                    </div>
                                </div>
                                
                                @endif
                            </div>  

                            @endforeach
                            @endif

                                        
                                                
                        </div>
                    </div>
                </div>

                <div class="sub-title mt-5">
                    <h2>Brand</h3>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        @if ($brands->isNotEmpty())
                            
                        @foreach ($brands as $brand )
                            
                    
                        <div class="form-check mb-2">
                            <input class="form-check-input brand-label" type="checkbox" name="brand[]" value="{{ $brand->id }}" id="brand-{{$brand->id}}" {{(in_array($brand->id,$brandsArray))? 'checked' : ''}}>
                            <label class="form-check-label" for="brand-{{$brand->id}}">
                                {{ $brand->name }}
                            </label>
                        </div>

                        @endforeach

                        @endif               
                    </div>
                </div>

                <div class="sub-title mt-5">
                    <h2>Price</h3>
                </div>
                
                <div class="card">
                    <div class="card-body">
                        <input type="text" class="js-range-slider" name="my_range" value="" />
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="row pb-3">
                    <div class="col-12 pb-1">
                        <div class="d-flex align-items-center justify-content-end mb-4">
                            <div class="ml-2">
                                <select name="sort" id="sort" class="form-control">
                                    <option value="">Sorting</option>
                                    <option value="latest" {{($sort == 'latest') ? 'selected' : ''}} >Latest</option>
                                    <option value="price_desc"  {{($sort == 'price_desc') ? 'selected' : ''}}>Price High</option>
                                    <option value="price_asc" {{($sort == 'price_asc') ? 'selected' : ''}}>Price Low</option> 
                                </select>                           
                            </div>
                        </div>
                    </div>

                    
          
                        @if ($products->isNotEmpty())
                        @foreach ($products as $product )
                        
                        
                        @php
                        $productimage  =  $product->productImages->first();
                        @endphp

                    
                    <div class="col-md-4">
                        <div class="card product-card">
                            <div class="product-image position-relative">

                                <a href="{{route('front.product',$product->slug)}}" class="product-img">
                                
                                @if (!empty($productimage->image))
                                <img class="card-img-top" src="{{asset('uploads/product/small/'.$productimage->image)}}" alt=""></a>
                                @else
                                <img class="card-img-top" src="{{asset('admin_files/img/default-150x150.png')}}" alt=""></a>
                                @endif


                                <a class="whishlist" href="222"><i class="far fa-heart"></i></a>                            

                                <div class="product-action">
                                    <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{$product->id}})">
                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                    </a>                            
                                </div>
                            </div>                        
                            <div class="card-body text-center mt-3">
                                <a class="h6 link" href="product.php">{{$product->name}}</a>
                                <div class="price mt-2">
                                    <span class="h5"><strong>{{$product->price}}</strong></span>
                                    @if ($product->compare_price > 0)
                                    <span class="h6 text-underline"><del>{{$product->compare_price}}</del></span>
                                        
                                    @endif
                                </div>
                            </div>                        
                        </div>                                               
                    </div>  


                   @endforeach
                   @endif

                    <div class="col-md-12 pt-5">
                        {{$products->withQueryString()->links('pagination::bootstrap-5')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection


@section('customJs')
    <script>
        
        rangeSlider = $(".js-range-slider").ionRangeSlider({
        type: "double",
        grid: true,
        min: 0,
        max: 5000,
        from: {{($price_min)}},
        step: 10,
        to: {{($price_max)}},
        skin: "round",
        max_postfix: "+",
        prefix: "$",
        onFinish : function() {
            apply_filters()
        }
       
        });


        //Saving it's instance to var
        var slider = $(".js-range-slider").data("ionRangeSlider");


            $(".brand-label").change(function(){
                    apply_filters();
            });

            
            $("#sort").change(function(){
                console.log($('#sort').val());
                apply_filters();
            });

                

            function apply_filters() {
                var brands = [];

                $(".brand-label").each(function(){
                    if ($(this).is(":checked")) {
                        brands.push($(this).val());
                    }
                });


                var url = new URL('{{ url()->current() }}?'); 

                // Add price_min and price_max to the URL
                url.searchParams.set('price_min', slider.result.from);
                url.searchParams.set('price_max', slider.result.to);

                // Check if brands array is not empty
                if (brands.length > 0) {
                    // Set 'brand' parameter in the URL if there are checked brands
                    url.searchParams.set('brand', brands.join(','));
                } else {
                    // Remove 'brand' parameter from the URL if no brands are checked
                    url.searchParams.delete('brand');
                }

                //sorting filter 
                url.searchParams.set('sort', $('#sort').val());


                // Redirect to the modified URL
                window.location.href = url.toString();            
        
        }



    </script>
@endsection