<?php 
class OrderController {
	function index() {
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($_SESSION["email"]);
		$orderRepository = new OrderRepository();
		$orders = $orderRepository->getByCustomerId($customer->getId());
		require ABSPATH_SITE .  "view/order/index.php";
	}

	function show() {
		$orderRepository = new OrderRepository();
		$order = $orderRepository->find($_GET["id"]);
		require ABSPATH_SITE .  "view/order/show.php";
	}
}