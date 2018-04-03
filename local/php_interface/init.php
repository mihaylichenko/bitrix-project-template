<?php
if(!file_exists(__DIR__.'/../vendor/autoload.php')){
    throw new \Bitrix\Main\Config\ConfigurationException('Use "composer install" in the /local folder');
}
require(__DIR__.'/../vendor/autoload.php');