<?php 
class PaymentController {
    function checkout() {
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        $email = "khachvanglai@gmail.com";
        if (!empty($_SESSION["email"])) {
            $email = $_SESSION["email"];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        require ABSPATH_SITE .  "layout/variable_address.php";
        require ABSPATH_SITE .  "view/payment/checkout.php";
    }

    function order() {
        //check đơn hàng (số lượng sản phẩm còn trong kho không)
        //sản phẩm không còn trong kho thì không cho đặt hàng
        $cartStorage = new CartStorage();
        $cart = $cartStorage->fetch();
        $items = $cart->getItems();
        $productRepository = new ProductRepository();
        
        foreach ($items as $item) {
            $product_id = $item["product_id"];
            $product= $productRepository->find($product_id);
            if ($product->getInventoryQty() < $item["qty"]) {
                $_SESSION["error"] = "{$product->getName()} chỉ còn {$product->getInventoryQty()} sản phẩm, bạn đặt hàng {$item["qty"]} sản phẩm";
                header("location: index.php");
                exit;
            }
            
        }
        //Lưu đơn hàng
        
        $email = "khachvanglai@gmail.com";
        if (!empty($_SESSION["email"])) {
            $email = $_SESSION["email"];
        }
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        $orderRepository = new OrderRepository();
        $provinceRepository = new ProvinceRepository();
		$province = $provinceRepository->find($_POST["province"]);
        $shipping_fee = $province->getShippingFee();
        $data = [] ;//later
        $data["created_date"] = date("Y-m-d H:i:s"); 
		$data["order_status_id"] = 1;//Đã đặt hàng
		$data["staff_id"] = null;
		$data["customer_id"] = $customer->getId();
		$data["shipping_fullname"] = $_POST["fullname"];
		$data["shipping_mobile"] = $_POST["mobile"];
		$data["payment_method"] = $_POST["payment_method"];
		$data["shipping_ward_id"] = $_POST["ward"];
		$data["shipping_housenumber_street"] =  $_POST["address"];
		$data["shipping_fee"] = $shipping_fee;
		$data["delivered_date"] = date("Y-m-d H:i:s", strtotime("+3 days"));

        $orderItemRepository = new OrderItemRepository();
        if ($orderId = $orderRepository->save($data)) {
            //Lưu các đơn hàng chi tiết
            $items = $cart->getItems();
            foreach($items as $item) {
                $dataItem = [];//later
                $dataItem["product_id"] = $item["product_id"]; 
                $dataItem["order_id"] = $orderId; 
                $dataItem["qty"] = $item["qty"]; 
                $dataItem["unit_price"] = $item["unit_price"]; 
                $dataItem["total_price"] = $item["total_price"]; 
                $orderItemRepository->save($dataItem);
                //Cập nhật lại kho hàng
                $product= $productRepository->find($dataItem["product_id"]);
                $updatedInventoryQty = $product->getInventoryQty() - $item["qty"];
                $product->setInventoryQty($updatedInventoryQty);
                $productRepository->update($product);

            }
            $_SESSION["success"] = "Bạn đã đặt hàng thành công";
            $cartStorage->clear();
        }
        else {
            $_SESSION["error"] = $orderRepository->getError();
        }

        header("location: index.php");
    }
}


?>