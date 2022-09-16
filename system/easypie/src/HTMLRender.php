<?php
/**
 * Renders HTML To UI
 */
namespace EaseRoutes;
class HTMLRender{
    static function render($pageLocation,$renderMap=false){
        $contents = file_get_contents($pageLocation);
        $homeDirectory = "";
        $new_contents = str_replace("{{HOME_DIR}}", $homeDirectory, $contents);
        if($renderMap!=false){
            foreach (@$renderMap as $key => $value) {
                $key="{{".$key."}}";
                $new_contents = str_replace($key, $value, $new_contents);
            }
        }
        return $new_contents;
    }

    static function renderFile($pageLocation,$arguments){
        #this class renders the file
        $fileContents       = file_get_contents($pageLocation);
        foreach ($arguments as $key => $value) {
            $fileContents   = str_replace("{{$key}}",$value,$fileContents);
        }
        return $fileContents;
    }
}

?>