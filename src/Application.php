<?php
declare(strict_types=1);

namespace App;

use Cake\Core\Configure;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\MissingPluginException;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Http\Middleware\BodyParserMiddleware;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\Middleware\EncryptedCookieMiddleware;
use Cake\Http\Middleware\RedirectMiddleware;
use Cake\Http\Middleware\SecurityHeadersMiddleware;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Authentication\AuthenticationService;
use Authentication\AuthenticationServiceProviderInterface;
use Authentication\Middleware\AuthenticationMiddleware;
use Psr\Http\Message\ServerRequestInterface;

class Application extends BaseApplication implements AuthenticationServiceProviderInterface
{
    public function bootstrap(): void
    {
        parent::bootstrap();
        
        $this->addPlugin('Authentication');
        $this->addPlugin('Authorization');
    }

    public function middleware($middlewareQueue): \Cake\Http\MiddlewareQueue
    {
        $errorConfig = Configure::read('Error');
        if ($errorConfig) {
            $errorConfig['exceptionRenderer'] = \Cake\Error\ExceptionRenderer::class;
            $errorConfig['skipLog'] = ['404'];
        }
        
        $middlewareQueue
            ->add(new ErrorHandlerMiddleware($errorConfig))
            ->add(new AssetMiddleware([
                'cacheTime' => Configure::read('Asset.cacheTime')
            ]))
            ->add(new RoutingMiddleware($this))
            ->add(new BodyParserMiddleware())
            ->add(new EncryptedCookieMiddleware(
                [],
                Configure::read('Security.cookieKey') ?: Security::getSalt()
            ))
            ->add(new CsrfProtectionMiddleware([
                'httponly' => true,
            ]))
            ->add(new AuthenticationMiddleware($this));

        return $middlewareQueue;
    }

    public function routes(RouteBuilder $routes): void
    {
        // Clear any existing routes first
        $routes->setRouteClass(\Cake\Routing\Route\DashedRoute::class);
        
        // Home page
        $routes->connect('/', ['controller' => 'Pages', 'action' => 'display', 'home']);

        // Public marketing landing page (no auth)
        $routes->connect('/rental-management', ['controller' => 'Landing', 'action' => 'index']);
        $routes->connect('/rental-management/', ['controller' => 'Landing', 'action' => 'index']);
        $routes->connect('/rental-management/index.html', ['controller' => 'Landing', 'action' => 'index']);
        $routes->connect('/rental-management/*', ['controller' => 'Landing', 'action' => 'index']);
        
        // Pages
        $routes->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);
        
        // Dashboard - explicit route
        $routes->connect('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);
        
        // Authentication routes
        $routes->connect('/login', ['controller' => 'Users', 'action' => 'login']);
        $routes->connect('/logout', ['controller' => 'Users', 'action' => 'logout']);
        $routes->connect('/register', ['controller' => 'Users', 'action' => 'register']);
        
        // API routes with prefix
        $routes->prefix('Api', function (RouteBuilder $routes) {
            $routes->setExtensions(['json']);
            
            // Firebase Phone Auth API routes
            $routes->connect('/firebase/send-otp', ['controller' => 'Api/Firebase', 'action' => 'sendOtp']);
            $routes->connect('/firebase/verify-otp', ['controller' => 'Api/Firebase', 'action' => 'verifyOtp']);
            
            // Fallback for other API routes
            $routes->fallbacks(\Cake\Routing\Route\DashedRoute::class);
        });
        
        // Standard fallback routes (must be last)
        $routes->fallbacks(\Cake\Routing\Route\DashedRoute::class);
    }

    public function getAuthenticationService(ServerRequestInterface $request): AuthenticationService
    {
        // Authentication service without automatic unauthenticatedRedirect.
        // Controllers (e.g. DashboardController) will handle redirects to login.
        $service = new AuthenticationService();

        $fields = [
            'username' => 'email',
            'password' => 'password'
        ];

        $service->loadIdentifier('Authentication.Password', [
            'fields' => $fields,
            'resolver' => [
                'className' => 'Authentication.Orm',
                'userModel' => 'Users',
            ],
        ]);

        // First, check for existing authenticated user in the session.
        $service->loadAuthenticator('Authentication.Session');

        // Then, use form authentication for login POSTs.
        $loginUrl = $request->getAttribute('base') . '/login';
        $service->loadAuthenticator('Authentication.Form', [
            'fields' => $fields,
            'loginUrl' => $loginUrl,
        ]);

        return $service;
    }

    public function services(ContainerInterface $container): void
    {
    }
}

