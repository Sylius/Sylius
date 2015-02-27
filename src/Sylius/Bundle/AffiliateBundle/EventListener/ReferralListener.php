<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle\EventListener;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Listener that looks for predefined referral variable in request & replace it
 * with cookie to save referral for next user visits.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ReferralListener
{
    private $affiliateRepository;
    private $queryParameter;
    private $cookieName;
    private $cookieLifetime;

    public function __construct(RepositoryInterface $affiliateRepository, $queryParameter, $cookieName, $cookieLifetime)
    {
        $this->affiliateRepository = $affiliateRepository;
        $this->queryParameter = $queryParameter;
        $this->cookieName = $cookieName;
        $this->cookieLifetime = $cookieLifetime;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->cookies->has($this->cookieName) && $request->query->has($this->queryParameter)) {
            $referralCode = $request->query->get($this->queryParameter);
            if (null !== $this->affiliateRepository->findOneBy(array('referralCode' => $referralCode))) {
                $response = $event->getResponse();
                $response->headers->setCookie(new Cookie($this->cookieName, $referralCode, new \DateTime($this->cookieLifetime)));

                $event->setResponse($response);
            }
        }
    }
}
