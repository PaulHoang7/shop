<?php 
use Firebase\JWT\JWT;
class CustomerController {
	function info() {
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($_SESSION["email"]);
		require ABSPATH_SITE .  "view/customer/info.php";
	}

	function updateInfo() {
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($_SESSION["email"]);
		$customer->setName($_POST["fullname"]);
		$customer->setMobile($_POST["mobile"]);
		$currentPassword = $_POST["current_password"];
		$dbPassword = $customer->getPassword();
		$newPassword = $_POST["password"];
		if ($currentPassword && $newPassword) {
			//verify
			if (password_verify($currentPassword, $dbPassword)) {
				$encodePassword = password_hash($newPassword, PASSWORD_BCRYPT);
				$customer->setPassword($encodePassword);
			}
			else {
				$_SESSION["error"] = "Mật khẩu hiện tại không đúng.";
				header("location: index.php?c=customer&a=info");
				exit;
			}
		}
		if ($customerRepository->update($customer)) {
			$_SESSION["name"] = $customer->getName();
			$_SESSION["success"] = "Đã cập nhật thông tin tài khoản thành công";
		}
		else {
			$_SESSION["error"] = $customerRepository->getError();
		}
		header("location: index.php?c=customer&a=info");
	}

	function shipping() {
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($_SESSION["email"]);
		require ABSPATH_SITE .  "layout/variable_address.php";
		require ABSPATH_SITE .  "view/customer/shipping.php";
	}

	function updateShipping() {
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($_SESSION["email"]);
		$customer->setShippingName($_POST["fullname"]);
		$customer->setShippingMobile($_POST["mobile"]);
		$customer->setWardId($_POST["ward"]);
		$customer->setHousenumberStreet($_POST["address"]);
		if ($customerRepository->update($customer)) {
			$_SESSION["name"] = $customer->getName();
			$_SESSION["success"] = "Đã cập nhật thông tin giao hàng mặc định thành công";
		}
		else {
			$_SESSION["error"] = $customerRepository->getError();
		}
		header("location: index.php?c=customer&a=shipping");
	}

	function forgotPassword() {
		//Gởi email để reset tài khoản
		$email = $_POST["email"];
		//check email existing
		$customerRepository = new CustomerRepository();
		$customer = $customerRepository->findEmail($email);
		if (!$customer) {
			$_SESSION["error"] = "$email không tồn tại";
			header("location: index.php");
			exit;
		}
		$mailServer = new MailService();

		$key = JWT_KEY;
		$payload = array(
			"email" => $email
		);
		$code = JWT::encode($payload, $key);
		$activeUrl= get_domain(). "/index.php?c=customer&a=resetPassword&code=$code";
		$content = "
			Chào $email, <br>
			Vui lòng click vào click vào link bên dưới để thiết lập lại password <br>
			<a href='$activeUrl'>Reset Password</a>
		";
		$mailServer->send($email, "Reset Password", $content);
		$_SESSION["success"] = "Vui lòng check email để reset password";
		header("location: index.php");
	}

	function resetPassword() {
		$code = $_GET["code"];
        try {
            $decoded = JWT::decode($code, JWT_KEY, array('HS256'));
            $email = $decoded->email;
            $customerRepository = new CustomerRepository();
            $customer = $customerRepository->findEmail($email);
            if (!$customer) {
                $_SESSION["error"] = "Email $email không tồn tại";
                header("location: /");
            }
            require ABSPATH_SITE .  "view/customer/resetPassword.php";
			// echo "No error";
        }
        catch(Exception $e) {
            echo "You try hack!";
        }
	}

	function updatePassword() {
		$code = $_POST["code"];
        try {
            $decoded = JWT::decode($code, JWT_KEY, array('HS256'));
            $email = $decoded->email;
            $customerRepository = new CustomerRepository();
            $customer = $customerRepository->findEmail($email);
			$newPassword = $_POST["password"];
			$hashNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $customer->setPassword($hashNewPassword);
			$customerRepository->update($customer);
			$_SESSION["success"] = "Password resets successfully";
			header("location: index.php");
			// echo "No error";
        }
        catch(Exception $e) {
            echo "You try hack!";
        }
	}
}
