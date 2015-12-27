<?php
/**
 * Created by PhpStorm.
 * User: explorer
 * Date: 2015/8/7
 * Time: 0:48
 */


use Scaffold\Controller\Controller;
use Scaffold\Validation\Validator;

class MainController extends Controller
{
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->middleWares=['csrf', 'auth'];
        //parent::__construct();
    }


    public function getList($id)
    {
        return $this->app->view->render('main', ['name'=>'liubingxia', 'email'=>'xiabingliu@163.com']);
    }

    public function validateData()
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
    }


}

