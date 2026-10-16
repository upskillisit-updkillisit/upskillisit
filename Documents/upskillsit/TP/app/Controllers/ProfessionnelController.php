<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Models\Application;
use App\Models\Content;
use App\Models\Profile;

final class ProfessionnelController extends Controller
{
    private function guard(): void
    {
        Auth::requireRole(['professional']);
    }

    public function index(): void
    {
        $this->render('dashboard', t('professional.space_title'));
    }

    public function profile(): void
    {
        $this->render('profile', t('nav.professional_profile'));
    }

    public function skills(): void
    {
        $this->render('skills', t('nav.skills'));
    }

    public function experiences(): void
    {
        $this->render('experiences', t('nav.experiences'));
    }

    public function projects(): void
    {
        $this->render('projects', t('nav.projects'));
    }

    public function opportunities(): void
    {
        $this->render('opportunities', t('nav.opportunities'));
    }

    public function applications(): void
    {
        $this->render('applications', t('nav.applications'));
    }

    public function resources(): void
    {
        $this->render('resources', t('nav.resources'));
    }

    public function events(): void
    {
        $this->render('events', t('nav.events'));
    }

    public function notifications(): void
    {
        $this->render('notifications', t('nav.notifications'));
    }

    public function settings(): void
    {
        $this->redirect('/compte');
    }

    public function saveProfile(): void
    {
        $this->guard();
        CSRF::requireValid();
        (new Profile())->saveProfessional((int) Auth::id(), $this->input());
        Session::flash('success', t('messages.professional_profile_updated'));
        $this->redirect('/professionnel/profil');
    }

    public function saveProject(): void
    {
        $this->guard();
        CSRF::requireValid();
        $profile = new Profile();
        $data = $this->input();
        $id = !empty($data['id']) ? (int) $data['id'] : null;
        $profile->saveProject((int) Auth::id(), $data, $id);
        Session::flash('success', t('messages.project_saved'));
        $this->redirect('/professionnel/experiences');
    }

    public function deleteProject(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        (new Profile())->deleteProject((int) Auth::id(), (int) $id);
        Session::flash('success', t('messages.project_deleted'));
        $this->redirect('/professionnel/experiences');
    }

    public function saveExperience(): void
    {
        $this->guard();
        CSRF::requireValid();
        $profile = new Profile();
        $data = $this->input();
        $id = !empty($data['id']) ? (int) $data['id'] : null;
        $profile->saveExperience((int) Auth::id(), $data, $id);
        Session::flash('success', t('messages.experience_saved'));
        $this->redirect('/professionnel/experiences');
    }

    public function deleteExperience(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        (new Profile())->deleteExperience((int) Auth::id(), (int) $id);
        Session::flash('success', t('messages.experience_deleted'));
        $this->redirect('/professionnel/experiences');
    }

    public function applyOpportunity(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        $result = (new Application())->apply((int) Auth::id(), (int) $id, $_POST['message'] ?? null);
        if ($result['ok']) {
            Session::flash('success', $result['message']);
        } else {
            Session::flash('error', $result['message']);
        }
        $this->redirect('/professionnel/opportunites');
    }

    private function render(string $section, string $title): void
    {
        $this->guard();
        $content = new Content();
        $profiles = new Profile();
        $application = new Application();
        $user = Auth::user();
        $userId = (int) Auth::id();
        $profile = $profiles->professional($userId) ?? [];
        $projects = $profiles->projects($userId);
        $experiences = $profiles->experiences($userId);
        $this->view('professionnel/index', [
            'title' => $title,
            'section' => $section,
            'user' => $user,
            'profile' => $profile,
            'completion' => $profiles->completion($profile),
            'projects' => $projects,
            'experiences' => $experiences,
            'applications' => $application->myApplications($userId),
            'appliedOpportunityIds' => $application->appliedOpportunityIds($userId),
            'applicationStats' => $application->statsForUser($userId),
            'notifications' => $profiles->notifications($userId),
            'opportunities' => array_slice($content->opportunities(), 0, $section === 'opportunities' ? 18 : 5),
            'resources' => array_slice($content->resources(), 0, $section === 'resources' ? 18 : 5),
            'events' => array_slice($content->events(), 0, $section === 'events' ? 18 : 5),
        ], 'dashboard');
    }
}
