<?php 

namespace App\Helpers;

use App\Http\Controllers\Controller;

class App extends Controller {

    public static function public_path($path = null)
    {
        return rtrim(app()->basePath('public/' . $path), '/');
    }

}