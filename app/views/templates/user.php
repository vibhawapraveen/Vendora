<a href="<?php if (isset(Session::user()['customer_id'])) {
                echo ROOT . $storecode . "/orders";
            } else {
                echo ROOT . "authcustomer?redirect_url=" . ROOT . $storecode . "/orders";
            } ?>" style="text-decoration: none; color: inherit;"><i class="fa-solid fa-user fa-lg"></i></a>