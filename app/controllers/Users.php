<?php

/*
 * The MIT License
 *
 * Copyright 2020 cjacobsen.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace App\Controllers;

/**
 * Description of Students
 *
 * @author cjacobsen
 */
use App\Models\District\DistrictUser;
use System\Post;
use System\App\Picture;
use System\Models\Post\UploadedFile;
use System\File;

class Users extends Controller {

    public function index() {
        return $this->search();
    }

    public function search($username = null) {
        if ($username == null) {
            return $this->view('users/search');
        } else {
            //var_export($username);

            return $this->showAccountStatus($username);
        }
    }

    public function searchPost($username = null) {
        //return $username;
        $action = Post::get("action");
        switch ($action) {
            case 'uploadPhoto':

                $uploadedPicture = new UploadedFile(Post::getFile("photo"));
                $picture = imagecreatefromjpeg($uploadedPicture->getTempFileName());
                $picture = Picture::cropSquare($picture, 225);
                ob_start();
                imagejpeg($picture);
                $rawPicture = ob_get_clean();
                var_dump(bin2hex($rawPicture));
                $user = new DistrictUser($username);
                $user->setPhoto($rawPicture);
                //imagecreatefromstring($uploadedPicture->getTempFileContents());
                //$resiezedPhoto = imagescale($picture, '96', '96');
//imagejpeg($picture);
                break;

            default:
                break;
        }
        return $this->search($username);
    }

    private function showAccountStatus($username) {

        $this->districtUser = $this->getUser($username);
        return $this->view('users/show');
    }

    public function getUser($username) {
        return new DistrictUser($username);
    }

    private function unlockUser($username) {
        $adUser = \App\Api\AD::get()->unlockUser($username);
        var_dump($adUser);
        return $adUser;
//$gaUser = \App\Api\GAM::getUser($username);
    }

    public function accountStatusChangePost() {
        if ($action = Post::get("action")) {
            $username = Post::get("username");
            switch ($action) {
                case "unlock":
                    $this->unlockUser($username);
                    $this->student = $this->getUser($username);
                    return $this->view('staff/show/student');
                    break;
                case "lock":
                    break;

                default:
                    break;
            }
        }
    }

    public function editPost() {
//if (Post::csrfValid()) {
        $username = Post::get("username");
        $districtUser = $this->getUser($username);
        var_dump($districtUser);
        $action = Post::get("action");
        var_dump($action);

        if ($action != false) {
            switch ($action) {
                case "unlock":
                    $districtUser->unlock();
                    $this->redirect('/users/search/' . $username);
                    break;
                //return $this->showAccountStatus($username);


                case "enable":
                    $districtUser->enable();
                    $this->redirect('/users/search/' . $username);
                    return;
                //return $this->showAccountStatus($username);
                case "disable";
                    $districtUser->disable();
                    $this->redirect('/users/search/' . $username);

                    //return $this->showAccountStatus($username);
                    return;

                default:
                    break;
            }
        }
    }

}
