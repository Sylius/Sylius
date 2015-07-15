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
class CreditCardSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Payment\Model\CreditCard');
    }

    public function it_implements_Sylius_credit_card_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Model\CreditCardInterface');
    }

    public function it_implements_Sylius_payment_source_interface()
    {
        $this->shouldImplement('Sylius\Component\Payment\Model\PaymentSourceInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_cardholder_name_by_default()
    {
        $this->getCardholderName()->shouldReturn(null);
    }

    public function its_cardholder_name_is_mutable()
    {
        $this->setCardholderName('John Doe');
        $this->getCardholderName()->shouldReturn('John Doe');
    }

    public function it_has_no_number_by_default()
    {
        $this->getNumber()->shouldReturn(null);
    }

    public function its_number_is_mutable()
    {
        $this->setNumber('5321');
        $this->getNumber()->shouldReturn('5321');
    }

    public function it_returns_last_4_digits_in_masked_number()
    {
        $this->setNumber('1244 1242 5434 1234');
        $this->getMaskedNumber()->shouldReturn('XXXX XXXX XXXX 1234');
    }

    public function it_returns_masked_number_when_converted_to_string()
    {
        $this->setNumber('1244 1242 5434 1234');
        $this->__toString()->shouldReturn('XXXX XXXX XXXX 1234');
    }

    public function it_has_no_security_code_by_default()
    {
        $this->getSecurityCode()->shouldReturn(null);
    }

    public function its_security_code_is_mutable()
    {
        $this->setSecurityCode('373');
        $this->getSecurityCode()->shouldReturn('373');
    }

    public function it_has_no_expiry_month_by_default()
    {
        $this->getExpiryMonth()->shouldReturn(null);
    }

    public function its_expiry_month_is_mutable()
    {
        $this->setExpiryMonth(11);
        $this->getExpiryMonth()->shouldReturn(11);
    }

    public function it_has_no_expiry_year_by_default()
    {
        $this->getExpiryYear()->shouldReturn(null);
    }

    public function its_expiry_year_is_mutable()
    {
        $this->setExpiryYear(15);
        $this->getExpiryYear()->shouldReturn(15);
    }

    public function it_has_no_token_by_default()
    {
        $this->getToken()->shouldReturn(null);
    }

    public function its_token_is_mutable()
    {
        $this->setToken('2sa42aaSOx');
        $this->getToken()->shouldReturn('2sa42aaSOx');
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
