<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Payment method mapped superclass spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PaymentMethod extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Entity\PaymentMethod');
    }

    function it_implements_Sylius_payment_method_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface');
    }

    function it_extends_Sylius_payment_method_model()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\PaymentMethod');
    }
}
