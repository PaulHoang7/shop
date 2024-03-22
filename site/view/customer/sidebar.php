<aside class="col-md-3">
    <div class="inner-aside">
        <div class="category">
            <?php 
            global $a;
            ?>
            <ul>
                <li class="<?=$a == "info" ? "active": ""?>">
                    <a href="index.php?c=customer&a=info" title="Thông tin tài khoản" target="_self">Thông tin
                        tài khoản
                    </a>
                </li >
                <li class="<?=$a == "shipping" ? "active": ""?>">
                    <a href="index.php?c=customer&a=shipping" title="Địa chỉ giao hàng mặc định" target="_self">Địa chỉ
                        giao hàng mặc định
                    </a>
                </li>
                <li class="<?=in_array($a, ["index", "show"]) ? "active": ""?>">
                    <a href="index.php?c=order&a=index" target="_self">Đơn hàng của tôi
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>