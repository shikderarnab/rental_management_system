<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;

/**
 * Fallback controller to handle incorrectly routed /rental-management segments.
 *
 * When the URL becomes /rental-management/rental-management/dashboard,
 * CakePHP interprets "rental-management" as the controller name.
 * This controller simply redirects those requests back to the real Dashboard.
 */
class RentalManagementController extends AppController
{
    public function index()
    {
        // Redirect /rental-management/rental-management to the main dashboard
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }

    public function dashboard()
    {
        // Redirect /rental-management/rental-management/dashboard to the main dashboard
        return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
    }
}


