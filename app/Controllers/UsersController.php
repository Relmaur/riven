<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\View;
use Core\Session;
use Core\Validator;
use Core\Security\Csrf;
use Core\Http\Request;
use Core\Http\RedirectResponse;
use App\Models\User;
use PgSql\Lob;

class UsersController extends BaseController
{
    /**
     * Show registration form
     */
    public function register(Request $request)
    {
        return View::render('users/register', [
            'pageTitle' => 'Register'
        ]);
    }

    /**
     * 
     */
    public function store(Request $request)
    {
        $validator = new Validator($request->all());

        $validator->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8']
        ]);

        // Check if email exitst
        if (User::findByEmail($request->input('email'))) {
            $validator->addError('email', 'This email address is already taken.');
        }

        if ($validator->fails()) {
            Session::flash('errors', $validator->getErrors());
            Session::flash('old_input', $request->all());
            return new RedirectResponse(route('register'));
        }

        // BEFORE Manual INSERT with password hashing
        // AFTER: One clean method call!
        User::register([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password')
        ]);

        Session::flash('success', 'Account created successfully! Please login');

        return new RedirectResponse(route('login'));
    }

    public function login(Request $request)
    {
        return View::render('users/login', [
            'pageTitle' => 'Login'
        ]);
    }

    public function authenticate(Request $request)
    {
        $validator = new Validator($request->all());

        $validator->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if ($validator->fails()) {
            Session::flash('errors', $validator->getErrors());
            Session::flash('old_input', $request->all());
            return new RedirectResponse(route('login'));
        }

        $user = User::findByEmail($request->input('email'));

        // BEFORE: Manual password_verify
        // AFTER: Clean method on the User model!
        if ($user && $user->verifyPassword($request->input('password'))) {
            Session::set('user_id', $user->id);
            Session::set('user_name', $user->name);

            Csrf::regenerateToken();

            Session::flash('success', 'Welcome Back, ' . $user->name . '!');
            return new RedirectResponse(route('dashboard'));
        } else {
            Session::flash('error', 'Invalid Credentials');
            return new RedirectResponse(route('login'));
        }
    }

    public function logout(Request $request)
    {
        Session::destroy();
        Session::flash('sucecess', 'You have been logged out.');
        return new RedirectResponse(route('home'));
    }
}
