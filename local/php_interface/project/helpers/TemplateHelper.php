<?php
namespace Project\Helpers;

class TemplateHelper
{
    /**
     * @return string
     */
    public static function getAssetsDir(){
        return SITE_TEMPLATE_PATH.'/html';
    }

    /**
     * @return bool
     */
    public static function isMainPage(){
        return \CSite::InDir('/index.php');
    }

}