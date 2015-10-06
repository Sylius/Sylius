<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Affiliate\Processor\ReferralProcessorInterface;
use Sylius\Component\Core\Affiliate\AffiliateContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Subscribes to all events that are referrable and handles their processing trough its processor.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class ReferralSubscriber implements EventSubscriberInterface
{
    /**
     * Referral processor.
     *
     * @var ReferralProcessorInterface
     */
    protected $referralProcessor;

    /**
     * Constructor.
     *
     * @param ReferralProcessorInterface $referralProcessor
     */
    public function __construct(ReferralProcessorInterface $referralProcessor, AffiliateContextInterface $affiliateContext)
    {
        $this->referralProcessor  = $referralProcessor;
        $this->affiliateContext   = $affiliateContext;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            'kernel.request'                => 'onKernelRequest',
            'sylius.customer.post_register' => 'onCustomerRegistration'
        );
    }

    /**
     * @param GenericEvent $event
     */
    public function onCustomerRegistration(GenericEvent $event)
    {
        if (!$this->affiliateContext->hasAffiliate()) {
            return;
        }

        if ($this->affiliateContext->hasAffiliate()) {
            $customer  = $event->getSubject();
            $affiliate = $this->affiliateContext->getAffiliate();

            $this->referralProcessor->process($customer, $affiliate);
        }
    }

    /**
     * @param KernelEvent $event
     */
    public function onKernelRequest(KernelEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if ($this->affiliateContext->hasAffiliate()) {
            $request   = $event->getRequest();
            $affiliate = $this->affiliateContext->getAffiliate();

            $this->referralProcessor->process($request, $affiliate);
        }
    }
}
