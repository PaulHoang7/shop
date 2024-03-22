<?php 
class LoginController {
    function form() {
        global $router;
        $email = $_POST["email"];
        $password = $_POST["password"];
        $customerRepository = new CustomerRepository();
        $customer = $customerRepository->findEmail($email);
        if ($customer) {
            $encodePassword = $customer->getPassword();
            if(password_verify($password, $encodePassword)) {
                //đúng mật khẩu
                //account đã active
                if ($customer->getIsActive()) {
                    $_SESSION["success"] = "Đăng nhập thành công";
                    $_SESSION["email"] = $email;
                    $_SESSION["name"] = $customer->getName();
                }
                else {
                    $_SESSION["error"] = "Vui lòng kích hoạt tài khoản bằng cách click vào link trong email đã đăng ký";
                }
                header("location:" . $router->generate('home'));
                exit;
            }
        }
        $_SESSION["error"] = "Vui lòng nhập lại email và mật khẩu";
        header("location: " . $router->generate('home'));
    }

    function google() {

    }

    function facebook() {
        
    }

    function logout() {
        global $router;
        session_destroy();
        header("location: " . $router->generate('home'));
    }

}
?>