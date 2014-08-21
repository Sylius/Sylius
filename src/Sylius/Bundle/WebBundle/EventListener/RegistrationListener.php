<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class RegistrationListener
 *
 * @author Dmitrijs Balabka <dmitry.balabka@gmail.com>
 */
class RegistrationListener
{
    /**
     * @var HttpUtils
     */
    protected $httpUtils;

    /**
     * @var string
     */
    protected $targetPathParameter = '_target_path';

    /**
     * @param HttpUtils $httpUtils
     */
    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    /**
     * @param FormEvent $event
     *
     * @return array
     */
    public function handleEvent(FormEvent $event)
    {
        $request = $event->getRequest();
        if ($url = $this->determineTargetUrl($request)) {
            $event->setResponse($this->httpUtils->createRedirectResponse($request, $url));
        }
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    protected function determineTargetUrl(Request $request)
    {
        return $request->request->get($this->targetPathParameter);
    }
}
