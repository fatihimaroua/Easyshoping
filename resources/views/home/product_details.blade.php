<!DOCTYPE html>
<html>
   <head>
      

      @include('home.css')

   </head>
   <body>
      @include('sweetalert::alert')
      <div class="hero_area">
         <!-- header section strats -->
       @include('home.header')
         <!-- end header section -->

   


       <div class="col-sm-6 col-md-4 col-lg-4" style="margin: auto; width: 50%; padding: 30px">
                 
                     <div class="img-box" style="padding: 20px">
                        <img width="300" src="/product/{{$product->image}}" alt="">
                     </div>
                     <div class="detail-box">
                        <h5>
                           {{$product->title}}
                        </h5>

                        @if($product->discount_price!=null)

                         <h6 style="color: red">
                           Discount price
                           <br>
                           ${{$product->discount_price}}
                        </h6>


                        <h6 style="text-decoration: line-through; color: blue">
                           Price
                           <br>
                           ${{$product->price}}
                        </h6>


                        @else

                        <h6 style="color: blue">
                           Price
                           <br>
                           ${{$product->price}}
                        </h6>



                        @endif

                        <h6>Product Catagory : {{$product->catagory}}</h6>

                        <h6>Product Details : {{$product->description}}</h6>

                        <h6>Available Quantity: {{$product->quantity}}</h6>

                        <form action="{{url('add_cart',$product->id)}}" method="Post">

                              @csrf

                              <div class="row">

                                 <div class="col-md-4">

                                    <input type="number" name="quantity" value="1" min="1" max="{{$product->quantity}}" style="width: 100px">

                                 </div>

                                 <div class="col-md-4">

                                    <input type="submit" value="Add To Cart">

                                 </div>
                                 
                                 

                              </div>

                           </form>


                        
                     </div>
                  </div>
               </div>




      <!-- footer start -->
      @include('home.footer')
      <!-- footer end -->
      
           <!-- jQery -->
      <script src="{{asset('home/js/jquery-3.4.1.min.js')}}"></script>
      <!-- popper js -->
      <script src="{{asset('home/js/popper.min.js')}}"></script>
      <!-- bootstrap js -->
      <script src="{{asset('home/js/bootstrap.js')}}"></script>
      <!-- custom js -->
      <script src="{{asset('home/js/custom.js')}}"></script>
   </body>
</html>