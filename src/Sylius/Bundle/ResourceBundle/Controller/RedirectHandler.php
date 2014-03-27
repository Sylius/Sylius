<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Redirects helper.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class RedirectHandler
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Configuration
     */
    private $config;

    /**
     * @var SessionInterface
     */
    private $session;

    public function __construct(Configuration $config, RouterInterface $router, SessionInterface $session)
    {
        $this->router = $router;
        $this->config = $config;
	$this->session = $session;
    }

    /**
     * @param  Request $request
     * @return void
     */
    public function handleRequest(Request $request)
    {
	if (
	    $request->isMethod('GET') &&
	    'referer' === $this->config->getRedirectRoute('show')
	) {
	    $referers = $this->session->get('sylius_resourse_referers', array());
	    $referer = $request->headers->get('referer');
	    $uriHash = md5($request->getUri());

	    if (!isset($referers[$uriHash]) || !$referers[$uriHash]) {
		$referers[$uriHash] = $referer;
		$this->session->set('sylius_resourse_referers', $referers);
	    }
	}
    }

    /**
     * @param object $resource
     *
     * @return RedirectResponse
     */
    public function redirectTo($resource)
    {
        $parameters = $this->config->getRedirectParameters($resource);

        return $this->redirectToRoute(
            $this->config->getRedirectRoute('show'),
            $parameters
        );
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToIndex()
    {
        return $this->redirectToRoute($this->config->getRedirectRoute('index'), $this->config->getRedirectParameters());
    }

    /**
     * @param string $route
     * @param array  $data
     *
     * @return RedirectResponse
     */
    public function redirectToRoute($route, array $data = array())
    {
        if ('referer' === $route) {
            return $this->redirectToReferer();
        }

        return $this->redirect($this->router->generate($route, $data));
    }

    /**
     * @param string  $url
     * @param integer $status
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToReferer()
    {
	$request = $this->config->getRequest();

	$referers = $this->session->get('sylius_resourse_referers', array());
	$uriHash = md5($request->getUri());

	if (isset($referers[$uriHash]) && $referers[$uriHash]) {
	    $referer = $referers[$uriHash];
	    unset($referers[$uriHash]);
	    $this->session->set('sylius_resourse_referers', $referers);

	    return $this->redirect($referer);
	}

        return $this->redirect($this->config->getRequest()->headers->get('referer'));
    }
}
