<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PaymentMethodSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Payment\Model\PaymentMethod');
    }

    public function it_implements_Sylius_payment_method_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Model\PaymentMethodInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Stripe');
        $this->getName()->shouldReturn('Stripe');
    }

    public function it_is_convertable_to_string_and_returns_its_name()
    {
        $this->setName('PayPal');
        $this->__toString()->shouldReturn('PayPal');
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription('Pay by check.');
        $this->getDescription()->shouldReturn('Pay by check.');
    }

    public function it_has_no_gateway_by_default()
    {
        $this->getGateway()->shouldReturn(null);
    }

    public function its_gateway_is_mutable()
    {
        $this->setGateway('paypal');
        $this->getGateway()->shouldReturn('paypal');
    }

    public function it_has_no_app_environment_by_default()
    {
        $this->getEnvironment()->shouldReturn(null);
    }

    public function its_app_environment_is_mutable()
    {
        $this->setEnvironment('dev');
        $this->getEnvironment()->shouldReturn('dev');
    }

    public function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    public function it_allows_disabling_itself()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
