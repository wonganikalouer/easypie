<?php
namespace EaseRoutes;

use stdClass;

/**
 * 
 */
class Response
{
    static function response($responseData, $status="success", $message="")
    {
        $data = new stdClass;
        $data->responseText = $responseData;
        $data->status = $status;
        $data->message = $message;
    
        return $data;
    }

    static function json($responseData, $status="success", $message="")
    {
        return json_encode(Response::response($responseData,$status,$message));
    }

    /**
     * @param string $viewLocation Locations from view eg. 'homepage' for homepage.php or 
     * 'admin.homepage' for admin/homepage.php
     */
    static function view($viewLocation, $dataVariables=[])
    {
        $newLocation = "views/".str_replace(".","/",$viewLocation).".php";
        extract($dataVariables);
        ob_start();
        include($newLocation);
        $output = ob_get_clean();
        print $output;
    }
}

