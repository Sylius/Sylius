<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\User\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\User\Model\CustomerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\User\Model\Customer');
    }

    function it_implements_customer_interface()
    {
        $this->shouldImplement(CustomerInterface::class);
    }

    function it_implements_user_aware_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Model\UserAwareInterface');
    }

    function it_implements_groupable_interface()
    {
        $this->shouldImplement('Sylius\Component\User\Model\GroupableInterface');
    }

    function it_sets_email()
    {
        $this->setEmail('customer@email.com');

        $this->getEmail()->shouldReturn('customer@email.com');
    }

    function it_sets_first_name()
    {
        $this->setFirstName('Edward');

        $this->getFirstName()->shouldReturn('Edward');
    }

    function it_sets_last_name()
    {
        $this->setLastName('Thatch');

        $this->getLastName()->shouldReturn('Thatch');
    }

    function it_can_get_full_name()
    {
        $this->setFirstName('Edward');
        $this->setLastName('Kenway');

        $this->getFullName()->shouldReturn('Edward Kenway');
    }

    function it_sets_birthday()
    {
        $birthday = new \DateTime('1987-07-08');
        $this->setBirthday($birthday);

        $this->getBirthday()->shouldReturn($birthday);
    }

    function it_sets_gender()
    {
        $this->setGender(CustomerInterface::FEMALE_GENDER);

        $this->getGender()->shouldReturn(CustomerInterface::FEMALE_GENDER);
    }

    function it_has_unknown_gender_as_unknown()
    {
        $this->getGender()->shouldReturn(CustomerInterface::UNKNOWN_GENDER);
    }

    function it_can_check_if_gender_is_female()
    {
        $this->setGender(CustomerInterface::FEMALE_GENDER);
        $this->isFemale()->shouldReturn(true);
        $this->isMale()->shouldReturn(false);
    }

    function it_can_check_if_gender_is_male()
    {
        $this->setGender(CustomerInterface::MALE_GENDER);
        $this->isFemale()->shouldReturn(false);
        $this->isMale()->shouldReturn(true);
    }
}
