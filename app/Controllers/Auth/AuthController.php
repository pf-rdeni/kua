<?php

namespace App\Controllers\Auth;

use Myth\Auth\Controllers\AuthController as MythAuthController;
use Myth\Auth\Entities\User;
use Myth\Auth\Models\UserModel;
use CodeIgniter\Session\Session;

class AuthController extends MythAuthController
{
    /**
     * Display the login page.
     * Overridden to prevent capturing 'previous_url' if it's the homepage,
     * so that successful login redirects to the dashboard (landingRoute).
     */
    public function login()
    {
        // No need to show a login form if the user
        // is already logged in.
        if ($this->auth->check()) {
            $redirectURL = session('redirect_url') ?? site_url($this->config->landingRoute);
            unset($_SESSION['redirect_url']);

            return redirect()->to($redirectURL);
        }

        // Set a return URL if none is specified
        // Custom: If previous_url is base_url, do NOT set valid redirect_url, 
        // so it falls back to landingRoute in attemptLogin (or we force it here).
        // Actually, attemptLogin uses 'session('redirect_url') ?? site_url('/')' 
        // We want it to use landingRoute.
        
        $previous = previous_url();
        $base = site_url();
        // Normalize
        $previous = rtrim($previous, '/');
        $base = rtrim($base, '/');

        if ($previous == $base) {
             // If coming from home, don't set redirect_url, let it default or set to landingRoute
             $_SESSION['redirect_url'] = site_url($this->config->landingRoute);
        } else {
             $_SESSION['redirect_url'] = session('redirect_url') ?? previous_url() ?? site_url($this->config->landingRoute);
        }

        return view($this->config->views['login'], ['config' => $this->config]);
    }
    
    /**
     * Attempts to verify the user's credentials.
     * Overridden to ensuring using landingRoute if redirect_url is not set or is home.
     */
    public function attemptLogin()
    {
        // ... (Validate logic same as parent) ...
        $rules = [
            'login'    => 'required',
            'password' => 'required',
        ];
        if ($this->config->validFields === ['email']) {
            $rules['login'] .= '|valid_email';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');
        $type = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (! $this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
            return redirect()->back()->withInput()->with('error', $this->auth->error() ?? lang('Auth.badAttempt'));
        }

        if ($this->auth->user()->force_pass_reset === true) {
            return redirect()->to(route_to('reset-password') . '?token=' . $this->auth->user()->reset_hash)->withCookies();
        }

        // CUSTOM REDIRECT LOGIC
        $redirectURL = session('redirect_url') ?? site_url($this->config->landingRoute);
        
        // If redirectURL is just '/', force landingRoute
        if ($redirectURL == site_url('/') || $redirectURL == site_url()) {
            $redirectURL = site_url($this->config->landingRoute);
        }
        
        unset($_SESSION['redirect_url']);

        return redirect()->to($redirectURL)->withCookies()->with('message', lang('Auth.loginSuccess'));
    }
}
