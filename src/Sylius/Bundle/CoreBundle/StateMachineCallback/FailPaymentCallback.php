<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\StateMachineCallback;

use Sylius\Component\Payment\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Flash error message of payum if payment failed.
 *
 * @author Ahmad Rabie <ahmad.rabei.ir@gmail.com>
 */
class FailPaymentCallback
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @param SessionInterface $session
     * @param TranslatorInterface $translator
     */
    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public function addFailFlash(PaymentInterface $payment)
    {

        $details = $payment->getDetails();
        if (isset($details['error']) && !empty($details['error'])) {
            $this->session->getBag('flashes')->add(
                'error',
                $this->translator->trans($details['error'], [], 'flashes')
            );
        }
    }
}
