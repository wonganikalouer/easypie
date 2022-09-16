<?php
namespace EaseRoutes;
use EaseRoutes\EasyPie;

require "Easypie.php";

class Routes {
    var $levels = 0;
    var $rootDir = "";
    private $notFound = false;

    public function __construct()
    {
        /**
         * automatically generates the number of levels and rootDirectory
         */
        $self = $_SERVER["SCRIPT_NAME"];
        $self = str_replace("/index.php","",$self);
        $self = substr($self,1);
        $this->rootDir = $self;
        $this->levels = count(explode("/",$self));
    }

    public function get($route,$page){
        return $this->redirect($route,$page);
    }
    private function redirect($route,$page,$parceableObjects = null){
        $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
        $rl = explode("?",$request_url);
        $GET_PARAMETERS =   "";
        if (count($rl)>1) {
            $GET_PARAMETERS =   $rl[1];
        }
        $request_url    =   $rl[0];
        $request_url = rtrim($request_url, '/');
        $request_url = strtok($request_url, '?');
        $route_parts = explode('/', $route);
        $request_url_parts = explode('/', $request_url);
        array_shift($route_parts);
        array_shift($request_url_parts);
        // echo $request_url."<br>";
        $ROOT = $_SERVER['DOCUMENT_ROOT']."/$this->rootDir";
        
        
        
        /**
         * Check if levels dont match and return, we dont need to continue from here
         */
        if( count($route_parts) != count($request_url_parts)-$this->levels ){ 
            // echo "Dont mind $route <br>";            
            return; 
        }

            
        $variables                  =   array();//parameters passed to the app through url
        $variables["routes"]        =   $request_url_parts;
        $variables["globalVars"]    =  $parceableObjects;
        $variables["ROOT_DIR"]      =  count($request_url_parts)-$this->levels;
        $variables["POST"]          =   $_POST;
        $variables["FILES"]          =   $_FILES;

        $variables                  =   $this->parseGetParameters($variables,$GET_PARAMETERS);
        for($i=$this->levels; $i<count($request_url_parts);$i++){
            $routeName              = $route_parts[$i-$this->levels];
            $isVariable2            =   substr($routeName,0,1);
            // echo $request_url_parts[$i];
            // print "'".$routeName."'";
            if($routeName!=$request_url_parts[$i] ){
                if($isVariable2==":"){
                    $variableName   =   explode(":",$route_parts[$i-$this->levels])[1];

                    // $par    =   array("".$variableName.""=>$request_url_parts[$i]);
                    $variables["".$variableName.""] = $request_url_parts[$i];//adding variables to our page from url
                }else{
                    return;
                }
            }
        }
        // die;
        // var_dump($variables);
        if(is_callable($page)){
            print call_user_func($page,$variables);
            die;
        }
        $this->loadWithVariables("$ROOT/$page",$variables);
        $this->notFound = false;
        exit();
    }

    private function parseGetParameters($vbs,$getVars){
        if($getVars==""){return $vbs;}
        $declarations = explode("&",$getVars);
        foreach ($declarations as $var) {
            $key        =  explode("=",$var)[0];
            $value      =  urldecode(explode("=",$var)[1]);
            $vbs[$key]  =   $value;
        }
        return $vbs;
    }
    private function loadWithVariables($pageFile,$variables){
        $output = NULL;
        extract($variables);
        ob_start();
        
        // print $pageFile;die;
        if (file_exists($pageFile)) {
            include $pageFile;
            $output = ob_get_clean();

            print($output);

            return $output;
        }else{
            echo "File <a href='#'>$pageFile</a> was not found";
        }
    }

    public function post($url,$pageToLoad){
        $csrf = @$_POST["_token"];
        unset($_POST["_token"]);
        if(EasyPie::validateToken($csrf)){
            return $this->get($url,$pageToLoad);
        }else{
            return "CSRF Error.";
        }
    }

    public function load404($pageToLoad=false){
        if (!$this->notFound) {
                $outputVariables = array("errorCode"=>404,"message"=>"Page not found");
                $this->loadWithVariables($pageToLoad, $outputVariables);
        }
    }
    static function savePost($postData){
        @session_start();
        $_SESSION["ease-routes-session-id"] = $postData;
    }

    static function getPost(){
        @session_start();
        return $_SESSION["ease-routes-session-id"];
    }

    static function clearPost(){
        $_SESSION["ease-routes-session-id"]=null;
    }
}
