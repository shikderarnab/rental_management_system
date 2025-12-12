<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;

class LandingController extends AppController
{
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow everyone to see the landing page without auth
        $this->Authentication->allowUnauthenticated(['index']);
        // Landing page does not need authorization checks
        $this->Authorization->skipAuthorization();
    }

    public function index()
    {
        $path = WWW_ROOT . 'rental-management' . DIRECTORY_SEPARATOR . 'index.html';

        if (!is_readable($path)) {
            throw new NotFoundException('Landing page not found.');
        }

        return $this->response->withFile($path);
    }
}


