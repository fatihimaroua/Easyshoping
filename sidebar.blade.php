<DOCTYPE html>
<html lang="en">
<head>
 @include( 'admin.css')
<style type ="text/css">
.div_center 
{
    text-align: center;
     padding-top: 40px;
}

.h2_font
{
    font-size: 40px; 
    padding-bottom: 40px;
}

.input_color
{
color: black;
}
.center
{
    margin: auto;
    width: 50;
    text-align: center;
    margin-top: 30px;
    border: 3px solid green;
}
</style>
</head>
<body>
<div class="container-scroller">
I
<!-- partial: partials/_sidebar.html --> @include('admin.sidebar')
<!-- partial -->
@include('admin.header')
<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        @if(session()->has('message'))
        <div class="alert alert success">
            <button type="button" class="close" data-dismiss="alert"
            aria-hidden="true">x</button>
            {{session()->get('message')}}
        </div>  
        @endif  
        <div class="div_center">
            <h2 class="h2_font">Add Catagory</h2>
            <form action="{{ url('/add-catagory')}}" method="POST"
            @csrf
             <input class="input_color" type="text" name="catagory" placeholder="Write Catagory name">
            <input type="submit" class="btn btn-primary" name="submit" value="Add Catagory">
          </form>
        </div>
        <table class="center">
            <tr>
                <td>Catagory Name</td>
                <td>Action</td>
            </tr>
        </table>
      </div>
    </div>  
    @include('admin.script')
   </body>
</html>