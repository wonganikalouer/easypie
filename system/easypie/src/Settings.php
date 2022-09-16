<?php
    namespace EaseRoutes;

    class Settings{
        static function read($defaultValue = true, $fetchLocalIfNoServer= true){
            $serverName = $_SERVER["SERVER_NAME"];
            $rawJSON = file_get_contents(".settings-server");
            if($serverName=="localhost" || $serverName=="127.0.0.1"){
                $rawJSON = file_get_contents(".settings");
            }
            // if($fetchLocalIfNoServer){
            //     $rawJSON = file_get_contents(".settings");
            // }
            return json_decode($rawJSON, $defaultValue);
        }

        static function get($key, $defaultValue="Could not find key passed", $fetchLocalIfNoServer= true){
            $d = Settings::read(true, $fetchLocalIfNoServer);
            if(empty($d[$key])){
                return $defaultValue;
            }else{
                return $d[$key];
            }
        } 
        
        static function print($key, $defaultValue="Could not find key passed", $fetchLocalIfNoServer= true){
            $d = Settings::read(true, $fetchLocalIfNoServer);
            if(empty($d[$key])){
                print $defaultValue;
            }else{
                print $d[$key];
            }
        } 
    }
    