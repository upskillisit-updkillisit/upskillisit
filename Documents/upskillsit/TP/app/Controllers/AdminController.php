<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Models\ContactMessage;
use App\Models\Content;
use App\Models\User;

final class AdminController extends Controller
{
    private function guard(): void { Auth::requireRole(['admin']); }

    public function index(): void
    {
        $this->guard();
        $content = new Content();
        $this->view('admin/index', [
            'title' => t('admin.dashboard_title'),
            'userStats' => (new User())->stats(),
            'contentStats' => $content->dashboardStats(),
            'applicationStats' => $content->applicationStats(),
            'messageStats' => (new ContactMessage())->stats(),
            'recentUsers' => (new User())->recent(6),
            'recentOpportunities' => array_slice($content->opportunities(['admin' => true]), 0, 6),
            'recentEvents' => array_slice($content->events(['admin' => true]), 0, 5),
            'recentArticles' => array_slice($content->articles(['admin' => true]), 0, 5),
        ], 'admin');
    }

    public function users(): void
    {
        $this->guard();
        $this->view('admin/users', ['title' => t('nav.users'), 'users' => (new User())->list($_GET)], 'admin');
    }

    public function userStatus(string $id): void
    {
        $this->guard(); CSRF::requireValid();
        (new User())->setStatus((int) $id, ($_POST['status'] ?? '') === 'suspended' ? 'suspended' : 'active');
        Session::flash('success', t('messages.user_status_updated'));
        $this->redirect('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        $this->guard(); CSRF::requireValid();
        if ((int) $id !== Auth::id()) { (new User())->delete((int) $id); }
        $this->redirect('/admin/users');
    }

    public function opportunities(): void { $this->contentPage(t('nav.opportunities'), 'opportunities'); }
    public function resources(): void { $this->contentPage(t('nav.resources'), 'resources'); }
    public function events(): void { $this->contentPage(t('nav.events'), 'events'); }
    public function articles(): void { $this->contentPage(t('nav.articles'), 'articles'); }

    public function statistics(): void
    {
        $this->guard();
        $content = new Content();
        $this->view('admin/statistics', [
            'title' => t('nav.statistics'),
            'userStats' => (new User())->stats(),
            'contentStats' => $content->dashboardStats(),
            'applicationStats' => $content->applicationStats(),
            'monthlyUsers' => (new User())->monthlyRegistrations(),
            'contentByStatus' => $content->contentByStatus(),
        ], 'admin');
    }

    public function settings(): void
    {
        $this->guard();
        $this->view('admin/settings', [
            'title' => t('nav.settings'),
            'userStats' => (new User())->stats(),
            'contentStats' => (new Content())->dashboardStats(),
        ], 'admin');
    }

    public function messages(): void
    {
        $this->guard();
        $messages = new ContactMessage();
        $this->view('admin/messages', [
            'title' => t('nav.messages'),
            'messages' => $messages->list($_GET),
            'messageStats' => $messages->stats(),
        ], 'admin');
    }

    public function messageStatus(string $id): void
    {
        $this->guard();
        CSRF::requireValid();
        (new ContactMessage())->setStatus((int) $id, $_POST['status'] ?? 'read');
        Session::flash('success', t('messages.message_updated'));
        $this->redirect('/admin/messages');
    }

    public function notifications(): void
    {
        $this->guard();
        $this->view('admin/notifications', [
            'title' => t('nav.notifications'),
            'messageStats' => (new ContactMessage())->stats(),
            'contentStats' => (new Content())->dashboardStats(),
        ], 'admin');
    }

    public function saveOpportunity(): void { $this->save('opportunity'); }
    public function saveResource(): void { $this->save('resource'); }
    public function saveEvent(): void { $this->save('event'); }
    public function saveArticle(): void { $this->save('article'); }
    public function deleteOpportunity(string $id): void { $this->delete('opportunities', (int) $id); }
    public function deleteResource(string $id): void { $this->delete('resources', (int) $id); }
    public function deleteEvent(string $id): void { $this->delete('events', (int) $id); }
    public function deleteArticle(string $id): void { $this->delete('articles', (int) $id); }
    public function statusOpportunity(string $id): void { $this->status('opportunities', (int) $id); }
    public function statusResource(string $id): void { $this->status('resources', (int) $id); }
    public function statusEvent(string $id): void { $this->status('events', (int) $id); }
    public function statusArticle(string $id): void { $this->status('articles', (int) $id); }

    private function contentPage(string $title, string $kind): void
    {
        $this->guard();
        $content = new Content();
        $items = match ($kind) {
            'resources' => $content->resources(['admin' => true]),
            'events' => $content->events(['admin' => true]),
            'articles' => $content->articles(['admin' => true]),
            default => $content->opportunities(['admin' => true]),
        };
        $edit = null;
        if (!empty($_GET['edit'])) {
            $method = match ($kind) {
                'opportunities' => 'opportunity',
                'resources' => 'resource',
                'events' => 'event',
                'articles' => 'article',
            };
            $edit = $content->$method((int) $_GET['edit']);
        }
        $this->view('admin/content', compact('title', 'kind', 'items', 'edit'), 'admin');
    }

    private function save(string $type): void
    {
        $this->guard(); CSRF::requireValid();
        $data = $this->input();
        if (!empty($data['event_date'])) { $data['event_date'] = str_replace('T', ' ', $data['event_date']); }
        if (($data['status'] ?? '') === 'published' && empty($data['published_at'])) { $data['published_at'] = date('Y-m-d H:i:s'); }
        $method = 'save' . ucfirst($type);
        (new Content())->$method($data, !empty($data['id']) ? (int) $data['id'] : null);
        Session::flash('success', t('messages.content_saved'));
        $this->redirect('/admin/' . match ($type) {
            'opportunity' => 'opportunites',
            'resource' => 'ressources',
            'event' => 'evenements',
            default => 'articles',
        });
    }

    private function delete(string $table, int $id): void
    {
        $this->guard(); CSRF::requireValid();
        (new Content())->delete($table, $id);
        $this->redirect($this->adminPath($table));
    }

    private function status(string $table, int $id): void
    {
        $this->guard(); CSRF::requireValid();
        $allowed = in_array($table, ['resources','articles'], true) ? ['draft','published'] : ['draft','published','closed'];
        $status = in_array($_POST['status'] ?? '', $allowed, true) ? $_POST['status'] : 'draft';
        (new Content())->setStatus($table, $id, $status);
        $this->redirect($this->adminPath($table));
    }

    private function adminPath(string $table): string
    {
        return '/admin/' . match ($table) {
            'opportunities' => 'opportunites',
            'resources' => 'ressources',
            'events' => 'evenements',
            default => $table,
        };
    }
}
