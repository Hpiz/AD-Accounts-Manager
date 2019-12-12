<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\controllers;

/**
 * Description of Login
 *
 * @author cjacobsen
 */
use system\app\auth\Local;
use system\app\App;
use app\models\user\User;
use system\app\Session;
use system\app\auth\AuthException;
use system\Post;
use app\config\MasterConfig;

class Login extends Controller {

    //put your code here

    public function index() {
        if (Post::isSet()) {
            try {
                /* @var $user User */
                $user = Local::authenticate(Post::get('username'), Post::get('password'));


                /** @var App|null The system logger */
                $app = App::get();
                $config = MasterConfig::get();

                $app->user = $user;


                Session::setUser($user);
                Session::updateTimeout();

                header("Location: /");
            } catch (AuthException $ex) {
                session_destroy();
                /* @var $ex AuthException */
                return $ex->getMessage();
            }
        } else {
            return $this->view('login/index');
        }
    }

}
