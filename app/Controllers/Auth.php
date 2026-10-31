<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\AuditLogModel;

class Auth extends BaseController
{
    // Purpose: Displays the login form or redirects users who are already signed in.
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    } 
    // End of login()

    // Purpose: Validates credentials, authenticates the user, and initializes the session.
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->getUserWithRole($email);

        if (! $user || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email address or password.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Your account is inactive or suspended. Please contact system admin.');
        }

        // Update login timestamp
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        // Audit Log
        (new AuditLogModel())->logAction($user['id'], 'LOGIN', 'User logged in successfully.');

        // Set Session
        session()->set([
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role_id'    => $user['role_id'],
            'role'       => strtolower($user['role_name']),
            'avatar'     => $user['avatar'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Welcome back, ' . $user['name'] . '!');
    } 
    // End of attemptLogin()

    // Purpose: Records the sign-out, destroys the session, and redirects to login.
    public function logout()
    {
        if (session()->get('user_id')) {
            (new AuditLogModel())->logAction(session()->get('user_id'), 'LOGOUT', 'User logged out.');
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    } 
    // End of logout()
}