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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    public function __construct(Configuration $config, RouterInterface $router)
    {
        $this->router = $router;
        $this->config = $config;
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
            $this->resolveResourceParameters($parameters, $resource)
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
     * @param string $url
     * @param int    $status
     *
     * @return RedirectResponse
     */
    public function redirect($url, $status = 302)
    {
        if ($this->config->isHeaderRedirection()) {
            return new Response('', 200, array(
                'X-SYLIUS-LOCATION' => $url.$this->config->getRedirectHash(),
            ));
        }

        return new RedirectResponse($url.$this->config->getRedirectHash(), $status);
    }

    /**
     * @return RedirectResponse
     */
    public function redirectToReferer()
    {
        return $this->redirect($this->config->getRedirectReferer());
    }

    /**
     * @param array  $parameters
     * @param object $resource
     *
     * @return array
     */
    private function resolveResourceParameters(array $parameters, $resource)
    {
        $accessor = PropertyAccess::createPropertyAccessor();

        if (empty($parameters)) {
            return array('id' => $accessor->getValue($resource, 'id'));
        }

        foreach ($parameters as $key => $value) {
            if (is_array($value)) {
                $parameters[$key] = $this->resolveResourceParameters($value, $resource);
            }

            if (is_string($value) && 0 === strpos($value, 'resource.')) {
                $parameters[$key] = $accessor->getValue($resource, substr($value, 9));
            }
        }

        return $parameters;
    }
}
