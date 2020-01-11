<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Payment\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentMethodSpec extends ObjectBehavior
{
    function let(): void
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_sylius_payment_method_interface(): void
    {
        $this->shouldImplement(PaymentMethodInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('PM1');
        $this->getCode()->shouldReturn('PM1');
    }

    function it_is_unnamed_by_default(): void
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Stripe');
        $this->getName()->shouldReturn('Stripe');
    }

    function it_is_convertible_to_string_and_returns_its_name(): void
    {
        $this->setName('PayPal');
        $this->__toString()->shouldReturn('PayPal');
    }

    function it_has_no_description_by_default(): void
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('Pay by check.');
        $this->getDescription()->shouldReturn('Pay by check.');
    }

    function its_instructions_is_mutable(): void
    {
        $this->setInstructions('Pay on account: 1100012312');
        $this->getInstructions()->shouldReturn('Pay on account: 1100012312');
    }

    function it_has_no_app_environment_by_default(): void
    {
        $this->getEnvironment()->shouldReturn(null);
    }

    function its_app_environment_is_mutable(): void
    {
        $this->setEnvironment('dev');
        $this->getEnvironment()->shouldReturn('dev');
    }

    function it_has_no_position_by_default(): void
    {
        $this->getPosition()->shouldReturn(null);
    }

    function its_position_is_mutable(): void
    {
        $this->setPosition(10);
        $this->getPosition()->shouldReturn(10);
    }

    function it_is_enabled_by_default(): void
    {
        $this->shouldBeEnabled();
    }

    function it_allows_disabling_itself(): void
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }

    function it_initializes_creation_date_by_default(): void
    {
        $this->getCreatedAt()->shouldHaveType(\DateTimeInterface::class);
    }

    function it_has_no_last_update_date_by_default(): void
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
