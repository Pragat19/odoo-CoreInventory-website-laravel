<?php

namespace App\Traits;

trait Constant
{
    public $successCode = 200;
    public $failCode = 200;

    /*
     * Images Paths
     * */
//    public $imageParentDirectory = '/api';
    public $imageParentDirectory = '';

    public $default_user_image = 'def_user.png';
    public $user_image_directory = '/images/users/';


}
