<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/7
 * Time: 0:48
 */

use Scaffold\Controller\Controller;

class MainController extends Controller
{
    public function getList($id)
    {
        $validator=Validator::make($_POST, [
            'id'=>'required|number',
            'name'=>'required|string',
            'email'=>'required|email',
            'password'=>'required|password'
        ]);

        if( $validator->fails() )
        {
            echo $validator->messages();
        }

        echo $id . "\n";
    }

}

