<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Models\User;

use App\Models\Product;

use App\Models\Cart;

use App\Models\Order;

use Session;

use Stripe;

use App\Models\Comment;

use App\Models\Reply;

use App\Models\Contact;

use RealRashid\SweetAlert\Facades\Alert;



class HomeController extends Controller
{   
  

    public function index()
    {

        if(Auth::id())
        {


            $usertype=Auth::user()->usertype;

        if($usertype=='1')
        {
            $total_product=product::all()->count();

            $total_order=order::all()->count();

            $total_user=user::all()->count();

            $order=order::all();

            $total_revenue=0;

            foreach($order as $order)

            {

                $total_revenue=$total_revenue + $order->price;


            }


       $total_delivered=order::where('delivery_status','=','delivered')->get()->count();


       $total_processing=order::where('delivery_status','=','processing')->get()->count();



            return view('admin.home',compact('total_product','total_order','total_user','total_revenue','total_delivered','total_processing'));
        }


             elseif($usertype=='0')

             {
                $product=Product::orderby('id','desc')->paginate(10);

                $comment=comment::orderby('id','desc')->get();



                $reply=reply::all();

                $user_id=Auth::user()->id;

                $cart_count=cart::where('user_id','=',$user_id)->count();

                
               
            return view('home.userpage',compact('product','comment','reply','cart_count'));

            }


        }

        else

        {

        $product=Product::orderby('id','desc')->paginate(6);

        
      $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();



        return view('home.userpage',compact('product','comment','reply'));


        }

    }


    public function redirect()
    {

    	$usertype=Auth::user()->usertype;

    	if($usertype=='1')
    	{
            $total_product=product::all()->count();

            $total_order=order::all()->count();

            $total_user=user::all()->count();

            $order=order::all();

            $total_revenue=0;

            foreach($order as $order)

            {

                $total_revenue=$total_revenue + $order->price;


            }


       $total_delivered=order::where('delivery_status','=','delivered')->get()->count();


       $total_processing=order::where('delivery_status','=','processing')->get()->count();



    		return view('admin.home',compact('total_product','total_order','total_user','total_revenue','total_delivered','total_processing'));
    	}

    	else
    	{
    		$product=Product::orderby('id','desc')->paginate(10);

            $comment=comment::orderby('id','desc')->get();



            $reply=reply::all();

            $user_id=Auth::user()->id;

            $cart_count=cart::where('user_id','=',$user_id)->count();

            
           
        return view('home.userpage',compact('product','comment','reply','cart_count'));
    	}
    }


    public function product_details($id)
    {
        if(Auth::id())
        {

          $product=product::find($id);

         $user_id=Auth::user()->id;

            $cart_count=cart::where('user_id','=',$user_id)->count();

        return view('home.product_details',compact('product','cart_count')); 
        }

        else
        {

            $product=product::find($id);

 

        return view('home.product_details',compact('product'));
        }

        

    }


    public function add_cart(Request $request,$id)
    {

        if(Auth::id())
        {

            $user=Auth::user();

            $userid=$user->id;

            $product=product::find($id);

            $product_exist_id=cart::where('Product_id','=',$id)->where('user_id','=',$userid)->get('id')->first();


            if($product_exist_id)
            {

                $cart=cart::find($product_exist_id)->first();

                $quantity=$cart->quantity;

                $cart->quantity=$quantity + $request->quantity;


                 if($product->discount_price!=null)

                        {
                            $cart->price=$product->discount_price * $cart->quantity;

                        }

                        else

                        {
                            $cart->price=$product->price * $cart->quantity;
                        }

                $cart->save();

              Alert::success('Product Added to Cart', 'Congrats!!! You\'ve Successfully Added Product to the cart');

                return redirect()->back(); 

            }

            else


            {

                             $cart=new cart;

                        $cart->name=$user->name;

                        $cart->email=$user->email;

                        $cart->phone=$user->phone;

                        $cart->address=$user->address;

                        $cart->user_id=$user->id;


                        $cart->product_title=$product->title;



                        if($product->discount_price!=null)

                        {
                            $cart->price=$product->discount_price * $request->quantity;

                        }

                        else

                        {
                            $cart->price=$product->price * $request->quantity;
                        }

                        

                         $cart->image=$product->image;

                          $cart->Product_id=$product->id;


                           $cart->quantity=$request->quantity;


                           $cart->save();

 Alert::success('Product Added to Cart', 'Congrats!!! You\'ve Successfully Added Product to the cart');
                          return redirect()->back();



            }


       

        }


        else

        {

            return redirect('login');

        }
    }


    public function show_cart()
    {

        if(Auth::id())

        {
             $id=Auth::user()->id;

             $cart_count=cart::where('user_id','=',$id)->count();

        $cart=cart::where('user_id','=',$id)->get();

        return view('home.showcart',compact('cart','cart_count'));

        }

        else
        {
            return redirect('login');
        }

       
    }


    public function remove_cart($id)
    {


        $cart=cart::find($id);


        $cart->delete();

   


        return redirect()->back();
    }


    public function order_cash()
    {

        $user=Auth::user();

         $userid=Auth::user()->id;

        $data=cart::where('user_id','=',$userid)->get();


       foreach($data as $data)


       {
            $order=new testorder;

                $order->product_title = $data->product_title;


                $order->save();

           


       }

           

      

        return redirect()->back();


    }


    public function cash_order($totalproduct)
    {

        if($totalproduct==0)

        {
            Alert::warning('No Product In Cart', 'Please Add some Product To the Cart');

             return redirect()->back();
        }

        else

        {


         $user=Auth::user();

        $userid=$user->id;


        $data=cart::where('user_id','=',$userid)->get();

        foreach($data as $data)
        {

            $order=new order;

            $order->name=$data->name;

            $order->email=$data->email;

            $order->phone=$data->phone;

            $order->address=$data->address;

            $order->user_id=$data->user_id;



            $order->product_title=$data->product_title;

            $order->price=$data->price;

            $order->quantity=$data->quantity;

            $order->image=$data->image;

            $order->product_id=$data->Product_id;


            $order->payment_status='cash on delivery';

            $order->delivery_status='processing';


            $order->save();




            $cart_id=$data->id;

            $cart=cart::find($cart_id);

            $cart->delete();



        }
        Alert::success('Thank You For your Order', 'We have Received your Order. We will connect with you soon...');

        return redirect()->back();


        }
      



    }


    public function stripe($totalprice)
    {

          if($totalprice==0)

        {
            Alert::warning('No Product In Cart', 'Please Add some Product To the Cart');

             return redirect()->back();
        }

        else


        {
             $userid=Auth::user()->id;

    $cart_count=cart::where('user_id','=',$userid)->count();

        return view('home.stripe',compact('totalprice','cart_count'));
        }

       
    }


    public function stripePost(Request $request,$totalprice)
    {

      
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Stripe\Charge::create ([
                "amount" => $totalprice * 100,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Thanks for payment." 
        ]);



        $user=Auth::user();

        $userid=$user->id;


        $data=cart::where('user_id','=',$userid)->get();

        foreach($data as $data)
        {

            $order=new order;

            $order->name=$data->name;

            $order->email=$data->email;

            $order->phone=$data->phone;

            $order->address=$data->address;

            $order->user_id=$data->user_id;



            $order->product_title=$data->product_title;

            $order->price=$data->price;

            $order->quantity=$data->quantity;

            $order->image=$data->image;

            $order->product_id=$data->Product_id;


            $order->payment_status='Paid';

            $order->delivery_status='processing';


            $order->save();




            $cart_id=$data->id;

            $cart=cart::find($cart_id);

            $cart->delete();



        }
      
        Alert::Success('Payment Successful', 'Thanks for the Order . We Will send you the Product Within 48 Hours.');
              
        return back();
    }




    public function show_order()
    {

        if(Auth::id())
        {
            $user=Auth::user();

            $userid=$user->id;

             $cart_count=cart::where('user_id','=',$userid)->count();

            $order=order::where('user_id','=',$userid)->get();

            return view('home.order',compact('order','cart_count'));

        }

        else

        {
             return redirect('login');
        }



    }

    public function cancel_order($id)
    {

        $order=order::find($id);

        $order->delivery_status='You canceled the order';


        $order->save();

        Alert::warning('Order Canceled', 'You Have Canceled Your Order');


        return redirect()->back();


    }


    public function add_comment(Request $request)
    {
            if(Auth::id())
            {

                $comment=new comment;


                $comment->name=Auth::user()->name;

                $comment->user_id=Auth::user()->id;

                $comment->comment=$request->comment;


                $comment->save();

                return redirect()->back();


            }

            else

            {

                return redirect('login');
            }


    }


    public function add_reply(Request $request)
    {

        if(Auth::id())
        {
            $reply=new reply;


            $reply->name=Auth::user()->name;

            $reply->user_id=Auth::user()->id;

            $reply->comment_id=$request->commentId;

            $reply->reply=$request->reply;

            $reply->save();

            return redirect()->back();

        }


        else

        {

            return redirect('login');

        }


    }


    public function product_search(Request $request)

    {   if(Auth::id())
        {
            $user_id=Auth::user()->id;

            $cart_count=cart::where('user_id','=',$user_id)->count();

        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $serach_text=$request->search;

        $product=product::where('title','LIKE',"%$serach_text%")->orWhere('catagory','LIKE',"$serach_text")->orderby('id','desc')->paginate(6);

        return view('home.userpage',compact('product','comment','reply','cart_count'));



        }


        else
        {

           

        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $serach_text=$request->search;

        $product=product::where('title','LIKE',"%$serach_text%")->orWhere('catagory','LIKE',"$serach_text")->orderby('id','desc')->paginate(6);;

        return view('home.userpage',compact('product','comment','reply'));
        }
        

    }

    public function product()
    {

        if(Auth::id())

        {

             $product=Product::paginate(10);

      $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $user_id=Auth::user()->id;

        $cart_count=cart::where('user_id','=',$user_id)->count();

        return view('home.all_product',compact('product','comment','reply','cart_count'));


        }


        else


        {


             $product=Product::paginate(10);

      $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        

        return view('home.all_product',compact('product','comment','reply'));
        }
        
    }


     public function search_product(Request $request)

    {   
        if(Auth::id())
        {

         $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        $user_id=Auth::user()->id;

        $cart_count=cart::where('user_id','=',$user_id)->count();

        $serach_text=$request->search;

        $product=product::where('title','LIKE',"%$serach_text%")->orWhere('catagory','LIKE',"$serach_text")->paginate(10);

        return view('home.all_product',compact('product','comment','reply','cart_count'));

        }


        else

        {

        $comment=comment::orderby('id','desc')->get();

        $reply=reply::all();

        

        $serach_text=$request->search;

        $product=product::where('title','LIKE',"%$serach_text%")->orWhere('catagory','LIKE',"$serach_text")->paginate(10);

        return view('home.all_product',compact('product','comment','reply'));

        }
  

    }


    public function contact()
    {   
        if(Auth::id())
        {



         $user_id=Auth::user()->id;

          $cart_count=cart::where('user_id','=',$user_id)->count();
        
        return view('home.contact',compact('cart_count'));
       
        }

        else
        {
 return view('home.contact');

        }

       
    }


    public function add_contact(Request $request)
    {


        $contact=new contact;

        $contact->name=$request->name;

        $contact->email=$request->email;

        $contact->subject=$request->subject;

        $contact->message=$request->message;

        $contact->save();

        Alert::success('Message Received', 'We will review your message and contact with you soon');

        return redirect()->back();


    }


    

 
}
