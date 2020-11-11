<?php

namespace LW;

class LW_Template
{
    private function __construct()
    {
    }

    public static function getTemplate($template_folder, $template_name, $attributes = null, $echo = true){
        if ( ! $attributes ) {
            $attributes = array();
        }

        ob_start();
        
        do_action( 'lummi_wild_before_' . $template_name );

        require( LW_PLUGIN_DIR. $template_folder. DIRECTORY_SEPARATOR . $template_name . '.php');

        do_action( 'lummi_wild_after_' . $template_name );

        $html = ob_get_contents();
        ob_end_clean();

        if($echo === false){
            return $html;
        }
        echo $html;
    }

}