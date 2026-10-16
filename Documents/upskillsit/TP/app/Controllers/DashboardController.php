<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Models\Application;
use App\Models\Content;
use App\Models\Profile;

final class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();
        $content = new Content();
        $profiles = new Profile();
        $user = Auth::user();
        $profile = $profiles->forUser((int) Auth::id(), $user['role']);
        $userId = (int) Auth::id();
        $applications = new Application();
        $this->view('dashboard/index', [
            'title' => t('nav.dashboard'),
            'user' => $user,
            'completion' => $profiles->completion($profile ?? []),
            'opportunities' => array_slice($content->opportunities(), 0, 4),
            'resources' => array_slice($content->resources(), 0, 4),
            'events' => array_slice($content->events(), 0, 4),
            'applications' => $applications->myApplications($userId),
            'applicationStats' => $applications->statsForUser($userId),
            'notifications' => $profiles->notifications($userId),
        ], 'dashboard');
    }

    public function profile(): void
    {
        Auth::requireAuth();
        $profiles = new Profile();
        $user = Auth::user();
        $profile = $profiles->forUser((int) Auth::id(), $user['role']);
        $this->view('dashboard/profile', ['title' => t('nav.profile'), 'user' => $user, 'profile' => $profile ?? [], 'projects' => $profiles->projects((int) Auth::id()), 'experiences' => $profiles->experiences((int) Auth::id())], 'dashboard');
    }

    public function applications(): void
    {
        Auth::requireRole(['student', 'professional']);
        $userId = (int) Auth::id();
        $applications = new Application();
        $this->view('dashboard/index', [
            'title' => t('dashboard.applications_title'),
            'user' => Auth::user(),
            'completion' => 0,
            'opportunities' => [],
            'resources' => [],
            'events' => [],
            'applications' => $applications->myApplications($userId),
            'applicationStats' => $applications->statsForUser($userId),
            'notifications' => (new Profile())->notifications($userId),
            'section' => 'applications',
        ], 'dashboard');
    }

    public function notifications(): void
    {
        Auth::requireAuth();
        $userId = (int) Auth::id();
        $this->view('dashboard/index', [
            'title' => t('dashboard.notifications_title'),
            'user' => Auth::user(),
            'completion' => 0,
            'opportunities' => [],
            'resources' => [],
            'events' => [],
            'applications' => [],
            'applicationStats' => [],
            'notifications' => (new Profile())->notifications($userId),
            'section' => 'notifications',
        ], 'dashboard');
    }

    public function applyOpportunity(string $id): void
    {
        Auth::requireRole(['student', 'professional']);
        CSRF::requireValid();
        $result = (new Application())->apply((int) Auth::id(), (int) $id, $_POST['message'] ?? null);
        Session::flash($result['ok'] ? 'success' : 'error', $result['message']);
        $this->redirect($result['ok'] ? '/candidatures' : '/opportunite/' . (int) $id);
    }

    public function saveProfile(): void
    {
        Auth::requireAuth(); CSRF::requireValid();
        $data = array_merge($this->input(), $this->uploads());
        $profile = new Profile();
        $role = (string) Auth::role();
        if ($role === 'professional') {
            $profile->saveProfessional((int) Auth::id(), $data);
        } elseif ($role === 'company') {
            $profile->saveCompany((int) Auth::id(), $data);
        } else {
            $profile->saveStudent((int) Auth::id(), $data);
            $profile->saveProjects((int) Auth::id(), $data['projects'] ?? []);
            $profile->saveExperiences((int) Auth::id(), $data['experiences'] ?? []);
        }
        Session::flash('success', t('messages.profile_updated'));
        $this->redirect('/profil');
    }

    private function uploads(): array
    {
        $result = [];
        foreach (['photo' => ['photo_dir','photo_mimes','max_photo_size','photo_path'], 'cv' => ['cv_dir','cv_mimes','max_cv_size','cv_path']] as $field => $cfg) {
            if (empty($_FILES[$field]['tmp_name'])) { continue; }
            [$dirKey, $mimeKey, $sizeKey, $targetField] = $cfg;
            $file = $_FILES[$field];
            $mime = mime_content_type($file['tmp_name']);
            if (!in_array($mime, config('uploads.' . $mimeKey), true) || $file['size'] > config('uploads.' . $sizeKey)) { continue; }
            $ext = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                default => 'bin',
            };
            $name = bin2hex(random_bytes(16)) . '.' . strtolower($ext);
            $dir = config('uploads.' . $dirKey);
            if (!is_dir($dir)) { mkdir($dir, 0775, true); }
            move_uploaded_file($file['tmp_name'], $dir . '/' . $name);
            $result[$targetField] = '/uploads/' . ($field === 'cv' ? 'cv' : 'photos') . '/' . $name;
        }
        return $result;
    }
}
