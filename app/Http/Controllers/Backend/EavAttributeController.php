<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/5/2018
 * Time: 9:29 AM
 */
namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;

class EavAttributeController extends Controller
{

    public function index(){
        return view('backend.eav_attribute.index');
    }

}