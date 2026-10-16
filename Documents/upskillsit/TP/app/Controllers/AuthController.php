<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

final class AuthController extends Controller
{
    public function registerForm(): void { $this->view('auth/register', ['title' => t('nav.register')]); }
    public function loginForm(): void { $this->view('auth/login', ['title' => t('nav.login')]); }

    public function register(): void
    {
        CSRF::requireValid();
        $data = $this->input();
        $v = (new Validator())->required($data, ['first_name','last_name','email','password','password_confirmation','role'])->email($data, 'email')->min($data, 'password', 8)->same($data, 'password', 'password_confirmation');
        if (empty($data['terms']) || empty($data['consent'])) { $v->errors['terms'] = t('messages.terms_required'); }
        if (!in_array($data['role'] ?? '', ['student','professional','company'], true)) { $v->errors['role'] = t('messages.invalid_profile_type'); }
        $users = new User();
        if ($users->findByEmail($data['email'] ?? '')) { $v->errors['email'] = t('messages.email_used'); }
        if (!$v->ok()) { $this->view('auth/register', ['title' => t('nav.register'), 'errors' => $v->errors, 'old' => $data]); return; }
        $id = $users->create($data);
        $user = $users->find($id);
        Auth::login($user);
        $this->redirect($this->roleHome($user['role'] ?? 'student'));
    }

    public function login(): void
    {
        CSRF::requireValid();
        $data = $this->input();
        $key = 'login_attempts_' . md5($_SERVER['REMOTE_ADDR'] ?? 'local');
        $attempts = Session::get($key, ['count' => 0, 'until' => 0]);
        if (($attempts['until'] ?? 0) > time()) { Session::flash('error', t('messages.too_many_attempts')); $this->redirect('/connexion'); }
        $user = (new User())->findByEmail($data['email'] ?? '');
        if (!$user || $user['status'] !== 'active' || !password_verify((string) ($data['password'] ?? ''), $user['password'])) {
            $attempts['count'] = ($attempts['count'] ?? 0) + 1;
            if ($attempts['count'] >= config('security.max_login_attempts')) { $attempts['until'] = time() + config('security.lockout_minutes') * 60; }
            Session::put($key, $attempts);
            Session::flash('error', t('messages.invalid_credentials'));
            $this->redirect('/connexion');
        }
        Session::forget($key);
        Auth::login($user);
        $this->redirect($this->roleHome($user['role']));
    }

    public function logout(): void { CSRF::requireValid(); Auth::logout(); header('Location: ' . url('/')); }
    public function forgotForm(): void { $this->view('auth/forgot', ['title' => t('auth.forgot_title')]); }

    public function forgot(): void
    {
        CSRF::requireValid();
        $token = (new User())->createResetToken((string) ($_POST['email'] ?? ''));
        $message = t('messages.reset_if_exists');
        if ($token) {
            $message .= ' ' . t('messages.local_link') . ': ' . url('/reinitialiser-mot-de-passe/' . $token);
        }
        Session::flash('success', $message);
        $this->redirect('/mot-de-passe-oublie');
    }

    public function resetForm(string $token): void
    {
        $this->view('auth/reset', ['title' => t('auth.reset_title'), 'token' => $token, 'valid' => (new User())->findResetToken($token) !== null]);
    }

    public function reset(string $token): void
    {
        CSRF::requireValid();
        $password = (string) ($_POST['password'] ?? '');
        if (strlen($password) < 8 || $password !== (string) ($_POST['password_confirmation'] ?? '')) {
            $this->view('auth/reset', ['title' => t('auth.reset_title'), 'token' => $token, 'valid' => true, 'error' => t('messages.password_confirm')]);
            return;
        }
        if (!(new User())->resetPassword($token, $password)) {
            $this->view('auth/reset', ['title' => t('auth.reset_title'), 'token' => $token, 'valid' => false]);
            return;
        }
        Session::flash('success', t('messages.password_updated'));
        $this->redirect('/connexion');
    }

    public function account(): void { Auth::requireAuth(); $this->view('auth/account', ['title' => t('auth.account_title'), 'user' => Auth::user()]); }
    public function updateAccount(): void
    {
        Auth::requireAuth(); CSRF::requireValid();
        try {
            (new User())->updateAccount((int) Auth::id(), $this->input());
            Session::flash('success', t('messages.account_updated'));
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
        }
        $this->redirect('/compte');
    }
    public function deleteAccount(): void { Auth::requireAuth(); CSRF::requireValid(); $id = (int) Auth::id(); Auth::logout(); (new User())->delete($id); header('Location: ' . url('/')); }

    private function roleHome(string $role): string
    {
        return match ($role) {
            'admin' => '/admin',
            'professional' => '/professionnel',
            'company' => '/entreprise',
            default => '/dashboard',
        };
    }
}
