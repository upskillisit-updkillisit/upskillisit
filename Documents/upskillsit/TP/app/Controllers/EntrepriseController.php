<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Models\Application;
use App\Models\Content;
use App\Models\Profile;
use App\Models\Talent;

final class EntrepriseController extends Controller
{
    private function guard(): void
    {
        Auth::requireRole(['company']);
    }

    public function index(): void
    {
        $this->render('dashboard', t('company.space_title'));
    }

    public function profile(): void
    {
        $this->render('profile', t('nav.company_profile'));
    }

    public function opportunities(): void
    {
        $this->render('opportunities', t('company.manage_opportunities'));
    }

    public function createOpportunity(): void
    {
        $this->render('opportunities', t('nav.create_opportunity'));
    }

    public function applications(): void
    {
        $this->render('applications', t('nav.applications'));
    }

    public function talents(): void
    {
        $this->render('talents', t('nav.talents'));
    }

    public function events(): void
    {
        $this->render('events', t('nav.events'));
    }

    public function settings(): void
    {
        $this->redirect('/compte');
    }

    public function saveProfile(): void
    {
        $this->guard();
        CSRF::requireValid();
        (new Profile())->saveCompany((int) Auth::id(), $this->input());
        Session::flash('success', t('messages.company_profile_updated'));
        $this->redirect('/entreprise/profil');
    }

    public function saveOpportunity(): void
    {
        $this->guard();
        CSRF::requireValid();
        $data = $this->input();
        $id = !empty($data['id']) ? (int) $data['id'] : null;
        if ($id && !(new Content())->companyOpportunity((int) Auth::id(), $id)) {
            Session::flash('error', t('messages.cannot_edit_opportunity'));
            $this->redirect('/entreprise/opportunites');
        }
        $data['company_user_id'] = Auth::id();
        $data['status'] = in_array($data['status'] ?? 'draft', ['draft', 'published', 'closed'], true) ? $data['status'] : 'draft';
        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }
        if (($data['status'] ?? '') === 'draft') {
            $data['published_at'] = null;
        }
        (new Content())->saveOpportunity($data, $id);
        Session::flash('success', t('messages.company_opportunity_saved'));
        $this->redirect('/entreprise/opportunites');
    }

    public function statusOpportunity(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        $status = $_POST['status'] ?? 'draft';
        $allowed = ['draft', 'published', 'closed'];
        if (!in_array($status, $allowed, true)) {
            $status = 'draft';
        }
        $content = new Content();
        $opportunity = $content->companyOpportunity((int) Auth::id(), (int) $id);
        if ($opportunity) {
            $content->setStatus('opportunities', (int) $id, $status);
            Session::flash('success', t('messages.opportunity_status_updated'));
        } else {
            Session::flash('error', t('messages.cannot_edit_opportunity'));
        }
        $this->redirect('/entreprise/opportunites');
    }

    public function deleteOpportunity(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        $content = new Content();
        $opportunity = $content->companyOpportunity((int) Auth::id(), (int) $id);
        if ($opportunity) {
            $content->delete('opportunities', (int) $id);
            Session::flash('success', t('messages.opportunity_deleted'));
        } else {
            Session::flash('error', t('messages.cannot_delete_opportunity'));
        }
        $this->redirect('/entreprise/opportunites');
    }

    public function applicationStatus(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        $updated = (new Application())->updateStatus((int) $id, (int) Auth::id(), $_POST['status'] ?? 'reviewed');
        Session::flash($updated ? 'success' : 'error', $updated ? t('messages.application_status_updated') : t('messages.application_not_found_company'));
        $this->redirect('/entreprise/candidatures');
    }

    private function render(string $section, string $title): void
    {
        $this->guard();
        $applications = new Application();
        $applications->ensureSchema();
        $content = new Content();
        $profiles = new Profile();
        $userId = (int) Auth::id();
        $profile = $profiles->company($userId) ?? [];
        $editingId = !empty($_GET['edit']) ? (int) $_GET['edit'] : null;
        $editingOpportunity = $editingId ? $content->companyOpportunity($userId, $editingId) : null;
        $this->view('entreprise/index', [
            'title' => $title,
            'section' => $section,
            'user' => Auth::user(),
            'profile' => $profile,
            'completion' => $profiles->completion($profile),
            'opportunities' => $content->companyOpportunities($userId),
            'applications' => $applications->companyApplications($userId),
            'applicationStats' => $applications->statsForCompany($userId),
            'talents' => (new Talent())->profiles($_GET),
            'events' => array_slice($content->events(), 0, $section === 'events' ? 18 : 5),
            'editingOpportunity' => $editingOpportunity,
        ], 'dashboard');
    }
}
