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

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Affiliate\Model\ReferralInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Listener that looks for predefined referral variable in request & replace it
 * with cookie to save referral for next user visits.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class ReferralListener
{
    private $securityContext;
    private $affiliateRepository;

    /**
     * @var string
     */
    private $queryParameter;

    /**
     * @var string
     */
    private $cookieName;

    /**
     * @var int
     */
    private $cookieLifetime;

    public function __construct(
        SecurityContextInterface $securityContext,
        RepositoryInterface $affiliateRepository,
        $queryParameter,
        $cookieName,
        $cookieLifetime
    ) {
        $this->securityContext = $securityContext;
        $this->affiliateRepository = $affiliateRepository;
        $this->queryParameter = $queryParameter;
        $this->cookieName = $cookieName;
        $this->cookieLifetime = $cookieLifetime;
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->cookies->has($this->cookieName) && $request->query->has($this->queryParameter)) {
            $referralCode = $request->query->get($this->queryParameter);
            /** @var $affiliate AffiliateInterface */
            if ($this->addReferral($referralCode)) {
                $response = $event->getResponse();
                $response->headers->setCookie(new Cookie($this->cookieName, $referralCode, new \DateTime($this->cookieLifetime)));

                $event->setResponse($response);
            }
        }
    }

    /**
     * @param string $referralCode
     *
     * @return bool
     */
    private function addReferral($referralCode)
    {
        $affiliate = $this->affiliateRepository->findOneBy(array('referralCode' => $referralCode, 'status' => AffiliateInterface::AFFILIATE_ENABLED));
        if ($affiliate && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $affiliate->addReferral($this->securityContext->getToken()->getUser());

            return true;
        }

        return false;
    }
}
