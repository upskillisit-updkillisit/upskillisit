<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\CSRF;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ContactMessage;
use App\Models\Content;

final class PublicController extends Controller
{
    public function home(): void
    {
        $content = new Content();
        $this->view('public/home', [
            'title' => t('home.title'),
            'opportunities' => array_slice($content->opportunities(), 0, 3),
            'events' => array_slice($content->events(), 0, 3),
            'articles' => $content->articles(),
        ]);
    }

    public function page(): void
    {
        $this->view('public/page', [
            'title' => $this->titleFromUri(),
            'slug' => trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'),
        ]);
    }

    public function contact(): void { $this->view('public/contact', ['title' => t('nav.contact')]); }

    public function sendContact(): void
    {
        CSRF::requireValid();
        $data = $this->input();
        $validator = (new Validator())->required($data, ['name', 'email', 'subject', 'message'])->email($data, 'email')->min($data, 'message', 10);
        if (!$validator->ok()) {
            Session::flash('error', t('messages.contact_invalid'));
            $this->redirect('/contact');
        }
        (new ContactMessage())->create($data);
        Session::flash('success', t('messages.contact_sent'));
        $this->redirect('/contact');
    }

    public function faq(): void { $this->view('public/faq', ['title' => 'FAQ']); }
    public function legal(): void { $this->view('public/legal', ['title' => t('footer.legal')]); }
    public function privacy(): void { $this->view('public/privacy', ['title' => t('footer.privacy')]); }
    public function articles(): void { $this->view('public/articles', ['title' => t('nav.news'), 'articles' => (new Content())->articles()]); }
    public function article(string $id): void { $this->detail('article', (int) $id); }
    public function opportunities(): void { $this->view('public/opportunities', ['title' => t('nav.opportunities'), 'items' => (new Content())->opportunities($_GET)]); }
    public function opportunity(string $id): void { $this->detail('opportunity', (int) $id); }
    public function resources(): void { $this->view('public/resources', ['title' => t('nav.resources'), 'items' => (new Content())->resources($_GET)]); }
    public function resource(string $id): void { $this->detail('resource', (int) $id); }
    public function events(): void { $this->view('public/events', ['title' => t('nav.events'), 'items' => (new Content())->events($_GET)]); }
    public function event(string $id): void { $this->detail('event', (int) $id); }

    public function registerEvent(string $id): void
    {
        if (!Auth::check()) {
            $this->json(['ok' => false, 'redirect' => url('/connexion'), 'message' => t('messages.login_required')], 401);
            return;
        }
        if (!CSRF::verify($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['_csrf'] ?? null)) {
            $this->json(['ok' => false, 'message' => t('messages.csrf_invalid')], 419);
            return;
        }
        $this->json((new Content())->registerEvent((int) $id, Auth::id()));
    }

    private function detail(string $type, int $id): void
    {
        $item = (new Content())->$type($id);
        if (!$item || (($item['status'] ?? '') !== 'published' && Auth::role() !== 'admin')) {
            http_response_code(404);
            require APP_PATH . '/Views/errors/404.php';
            return;
        }
        $this->view('public/detail', ['title' => $item['title'], 'item' => $item, 'type' => $type]);
    }

    private function titleFromUri(): string
    {
        return match (trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/')) {
            'a-propos' => 'UpSkillsIT',
            'services' => t('nav.services'),
            'etudiants' => t('footer.students'),
            'professionnels-it' => t('footer.professionals'),
            'entreprises' => t('footer.companies'),
            default => 'UpSkillsIT',
        };
    }
}
