<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PaymentsBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface;

/**
 * Payment method model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PaymentMethod extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PaymentsBundle\Model\PaymentMethod');
    }

    function it_should_implement_Sylius_payment_method_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_be_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_should_be_mutable()
    {
        $this->setName('Stripe');
        $this->getName()->shouldReturn('Stripe');
    }

    function it_should_be_convertable_to_string_and_use_its_name_for_this()
    {
        $this->setName('PayPal');
        $this->__toString()->shouldReturn('PayPal');
    }

    function it_should_not_have_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_should_be_mutable()
    {
        $this->setDescription('Pay by check.');
        $this->getDescription()->shouldReturn('Pay by check.');
    }

    function it_should_not_require_Sylius_environment_by_default()
    {
        $this->getEnvironment()->shouldReturn(null);
    }

    function its_Sylius_environment_should_be_mutable()
    {
        $this->setEnvironment('dev');
        $this->getEnvironment()->shouldReturn('dev');
    }

    function it_should_not_require_gateway_by_default()
    {
        $this->getGateway()->shouldReturn(null);
    }

    function its_gateway_should_be_mutable()
    {
        $this->setGateway('dev');
        $this->getGateway()->shouldReturn('dev');
    }

    function it_should_be_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_should_allow_disabling_itself()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
