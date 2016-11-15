<?php
namespace App\Controllers;

use App\Controllers\Controller;
use App\Models\User;
use App\Services\Validator\Validator;
use App\Auth\Auth;

class AuthController extends Controller
{
    public function login()
    {
        $postForm = isset($_POST['login']) ? $_POST['login'] : null;
            if ($postForm){
                if (Auth::login($postForm)){
                    if (isset($postForm['rememberMe'])){
                        setcookie('password', md5($postForm['pass']), time()+9999999 );
                    }

                    header('location: /account');
                }
            }
        include "app/Views/header.html.php";
        include "app/Views/login.html.php";
    }
    public function registration()
    {

        $postForm = isset($_POST['reg']) ? $_POST['reg'] : null;
        if ($postForm){
            $validation = new Validator();
            $validateCheck = $validation->validation(
                ['name'=>['require', 'string'],
                'email'=>['email', 'require'],
                'password'=>['require', 'string']],
                ['name'=>$postForm['name'], 'email'=>$postForm['email'], 'password'=>$postForm['password']]);
            if ($validateCheck){
                $user = new User();
                $user->setName($postForm['name']);
                $user->setEmail($postForm['email']);
                $user->setPassword(md5($postForm['password']));
                Auth::register($user);
            }

        }
        include "app/Views/header.html.php";
        include "app/Views/registration.html.php";
    }
}