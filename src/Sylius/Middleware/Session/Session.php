<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Middleware\Locale;

use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Original code by Igor Wiedler.
 *
 * @link https://github.com/stackphp/session
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class Session implements HttpKernelInterface
{
    /**
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(HttpKernelInterface $app, SettingsManagerInterface $settingsManager)
    {
        $this->app = $app;
        // @todo change this after refactoring of SettingsBundle
        // $this->settings = $settingsManager->loadSettings('general');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $type) {
            return $this->app->handle($request, $type, $catch);
        }

        $session = $this->session;
        $request->setSession($session);

        if ($request->cookies->has($session->getName())) {
            $session->setId($request->cookies->get($session->getName()));
        } else {
            $session->migrate(false);
        }

        $response = $this->app->handle($request, $type, $catch);

        if ($session && $session->isStarted()) {
            $session->save();
            $params = array_merge(
                session_get_cookie_params(),
                $this->settings->get('session.cookie_params')
            );

            $response->headers->setCookie(new Cookie(
                $session->getName(),
                $session->getId(),
                0 === $params['lifetime'] ? 0 : $request->server->get('REQUEST_TIME') + $params['lifetime'],
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            ));
        }

        return $response;
    }
}
