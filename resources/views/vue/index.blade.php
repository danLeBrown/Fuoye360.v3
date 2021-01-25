<?php 
// if (isset($current)) {
//     $current = $current;
// }else {
//     $current = null;
// }
error_reporting(E_ALL); 
$metaShopStatus = false;
$metaTags = array(
    'title' => 'FUOYE360 - Welcome to FUOYE360!',
    'url' => "https://fuoye360.com",
    'desc' => 'Think Differently. Innovate Differently. Proivde solutions rather than discuss problems. And while you do these things, remember that your greatest strength lies in doing things according to your own unique abilities. These are the WORDS we LIVE and BREATHE by.',
    'image' => asset('assets/images/img7.jpg'),
);
if (Request::is('/')) {
    $current = 'home';
}elseif (Request::is('/about')) {
    $current = 'about';
}elseif (Request::is('/shop/*')) {
    $current = 'shop';
}elseif (Request::is('/broadcast/*')) {
    $current = 'broadcast';
}else{
    $current = 'home';
}

if($current == 'about'){
    $metaTags['title'] = "FUOYE360 About | Check out our awesome developer";
    $metaTags['image'] = asset('assets/images/about.jpg');
    $metaTags['url'] = "https://fuoye360.com/about";
}elseif ($current == 'blog') {
    $metaTags['url'] = "https://fuoye360.com/blog";
    $metaTags['title'] = "FUOYE360 Blog | If you like Twitter you are going to love us.";
}elseif ($current == 'login') {
    $metaTags['title'] = "FUOYE360 Login | Welcome back to FUOYE360. We've missed you!";
    $metaTags['url'] = "https://fuoye360.com/login";
}elseif ($current == 'register') {
    $metaTags['url'] = "https://fuoye360.com/register";
    $metaTags['title'] = "FUOYE360 Create An Account | Join other awesome users to be part of the community!";
}else if($current == 'shop'){
    $metaTags['url'] = "https://fuoye360.com/shop";
    $metaTags['title'] = 'FUOYE360 Shop | All your favourite products and service providers in one place.';
    if($shopAction == 'buy'){
    $metaTags['url'] = "https://fuoye360.com/shop/buy";
        if(isset($_GET['pid']) && is_numeric(htmlspecialchars($_GET['pid']))){
            $metaShopStatus = true;
            $productId = $_GET['pid'];
            $metaTags['url'] = "https://fuoye360.com/shop/buy?pid=".$productId;
            $product = App\Product:: find($productId);
            $metaTags['title'] = $product->name;
            $metaTags['image'] = asset('storage/product_images/'.$product->image);
            $metaTags['desc'] = "₦".$product->price." | ".Str::upper($product->category)." | ".$product->views." views | ".substr($product->description, 0, 50)."...";
        }
    }
}
?>

<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <meta name="theme-color" content="#111">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">    
    <script>window.Laravel = {csrfToken : '{{csrf_token()}}'}</script>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&family=Montserrat+Alternates&display=swap" rel="stylesheet"> 
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet"> 
    <link rel="shortcut icon" type="imge/png" href="{{ asset('assets/images/favicon.png')}}">
    
    <!-- Styles -->
    <link href="{{ asset('css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}?v=1.{{rand(1,1000)}}" rel="stylesheet">
    <script>
        window._asset = '{{asset('')}}';
    </script>
</head>
<body>
    <div id="app">
    </div>
    <script src="{{ asset('js/app.js') }}?v=1.{{rand(1,1000)}}"></script>
    <script src="{{ asset('js/swiped-events.js') }}"></script>
</body>
</html>
