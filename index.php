<?php 
session_start();
require_once "config.php";
require_once ABSPATH . "bootstrap.php";
require_once ABSPATH_SITE . "load.php";
require 'vendor/autoload.php';
$router = new AltoRouter();

// Trang chủ
$router->map( 'GET', '/', ["HomeController", "index"], "home");

// Danh sách sản phẩm
$router->map( 'GET', '/san-pham', ["ProductController", "index"], "product");

//trang chính sách đổi trả
$router->map( 'GET', '/chinh-sach-doi-tra.html', array("InformationController", "returnPolicy"), 'returnPolicy');

// trang chính sách thanh toán
$router->map( 'GET', '/chinh-sach-thanh-toan.html', array("InformationController", "paymentPolicy"), 'paymentPolicy');

// trang chính sách giao hàng
$router->map( 'GET', '/chinh-sach-giao-hang.html', array("InformationController", "deliveryPolicy"), 'deliveryPolicy');

// trang liên hệ
$router->map( 'GET', '/lien-he.html', array("ContactController", "form"), 'contact-form');

// trang chi tiết sản phẩm
// không được dùng slug-name do không hiểu dấu - trong tên
$router->map('GET', '/san-pham/[*:slugName]-[i:id].html', function($slugName, $id) {
	$_GET["id"] = $id;
  	call_user_func_array(["ProductController", "show"],[]);
}, 'product-detail');

// trang danh mục
// không đực dùng slug-name do không được đặt tên biến có dấu -
// danh-muc/kem-chong-nang-1
$router->map('GET', '/danh-muc/[*:slugName]-[i:categoryId]', function($slugName, $categoryId) {
	$_GET["category_id"] = $categoryId;
  	call_user_func_array(["ProductController", "index"],[]);
}, 'category');

// khoảng giá
// khoang-gia/200000-300000
$router->map('GET', '/khoang-gia/[*:priceRange]', function($priceRange) {
	$_GET["price-range"] = $priceRange;
  	call_user_func_array(["ProductController", "index"],[]);
}, 'price-range');

// Tìm kiếm
$router->map('GET', '/search', function() {
  call_user_func_array(["ProductController", "index"],[]);
}, 'search');

// match current request url
$match = $router->match();
// $routeName = !empty($match["name"]) ? $match["name"] : null;
$routeName = $match["name"] ?? null;

// call closure or throw 404 status
if( is_array($match) && is_callable( $match['target'] ) ) {
	call_user_func_array( $match['target'], $match['params'] ); 
} else {
	// no route was matched
	// header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
	//router
  $c = $_GET["c"] ?? "home";
  $a = $_GET["a"] ?? "index";
  $controllerName = ucfirst($c). "Controller";//HomeController
  // require "controller/" . $controllerName . ".php";
  $controller = new $controllerName();//new HomeController();
  $controller->$a();//$controller->index();
}


function slugify($str)
{
  	$str = trim(mb_strtolower($str));
    $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
    $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
    $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
    $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
    $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
    $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
    $str = preg_replace('/(đ)/', 'd', $str);
    $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
    $str = preg_replace('/([\s]+)/', '-', $str);
    return $str;
}
?>