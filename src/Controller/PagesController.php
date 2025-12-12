<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Core\Configure;

class PagesController extends AppController
{
    public function display(string ...$path): ?\Cake\Http\Response
    {
        if (!$path) {
            return $this->redirect('/dashboard');
        }
        
        $page = $subpage = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        $this->set(compact('page', 'subpage'));

        try {
            return $this->render(implode('/', $path));
        } catch (\Cake\View\Exception\MissingTemplateException $exception) {
            if (Configure::read('debug')) {
                throw $exception;
            }
            throw new \Cake\Http\Exception\NotFoundException(__('Page not found.'));
        }
    }
}

