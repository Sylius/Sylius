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
use Sylius\Component\Payment\Model\PaymentMethod;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PaymentMethodSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PaymentMethod::class);
    }

    function it_implements_sylius_payment_method_interface()
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('PM1');
        $this->getCode()->shouldReturn('PM1');
    }

    function it_is_unnamed_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Stripe');
        $this->getName()->shouldReturn('Stripe');
    }

    function it_is_convertible_to_string_and_returns_its_name()
    {
        $this->setName('PayPal');
        $this->__toString()->shouldReturn('PayPal');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('Pay by check.');
        $this->getDescription()->shouldReturn('Pay by check.');
    }

    function its_instructions_is_mutable()
    {
        $this->setInstructions('Pay on account: 1100012312');
        $this->getInstructions()->shouldReturn('Pay on account: 1100012312');
    }

    function it_has_no_app_environment_by_default()
    {
        $this->getEnvironment()->shouldReturn(null);
    }

    function its_app_environment_is_mutable()
    {
        $this->setEnvironment('dev');
        $this->getEnvironment()->shouldReturn('dev');
    }

    function it_has_no_position_by_default()
    {
        $this->getPosition()->shouldReturn(null);
    }

    function its_position_is_mutable()
    {
        $this->setPosition(10);
        $this->getPosition()->shouldReturn(10);
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_allows_disabling_itself()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
