<?php
    namespace EaseRoutes;
    class EasyPie{
        static function csrf(){
            @session_start();
            $UNIQUE_KEY = date("mdhs");
            $CSRF = "";
            if(isset($_SESSION["CSRF"])){
                $CSRF = $_SESSION["CSRF"];
            }else{
                $_SESSION["CSRF"] = md5(password_hash($UNIQUE_KEY,PASSWORD_BCRYPT));
                $CSRF = $_SESSION["CSRF"];
            }
            print "<input type='hidden' value='$CSRF' id='_token' name='_token'/>";
        }
        static function print()
        {
            return EasyPie::csrf();
        }
        static function getCSRF(){
            @session_start();
            return @$_SESSION["CSRF"];
        }

        /**
         * @param string $token is the cross refference token
         * @return bool valid token or not
         */
        static function validateToken($token){
            return $token==EasyPie::getCSRF();
        }
    }