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

namespace spec\Sylius\Component\Customer\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Sylius\Component\Customer\Model\CustomerInterface;

final class CustomerSpec extends ObjectBehavior
{
    function it_implements_customer_interface(): void
    {
        $this->shouldImplement(CustomerInterface::class);
    }

    function it_sets_email(): void
    {
        $this->setEmail('customer@email.com');

        $this->getEmail()->shouldReturn('customer@email.com');
    }

    function it_sets_first_name(): void
    {
        $this->setFirstName('Edward');

        $this->getFirstName()->shouldReturn('Edward');
    }

    function it_sets_last_name(): void
    {
        $this->setLastName('Thatch');

        $this->getLastName()->shouldReturn('Thatch');
    }

    function it_can_get_full_name(): void
    {
        $this->setFirstName('Edward');
        $this->setLastName('Kenway');

        $this->getFullName()->shouldReturn('Edward Kenway');
    }

    function it_sets_birthday(): void
    {
        $birthday = new \DateTime('1987-07-08');
        $this->setBirthday($birthday);

        $this->getBirthday()->shouldReturn($birthday);
    }

    function it_sets_gender(): void
    {
        $this->setGender(CustomerInterface::FEMALE_GENDER);

        $this->getGender()->shouldReturn(CustomerInterface::FEMALE_GENDER);
    }

    function it_has_unknown_gender_as_unknown(): void
    {
        $this->getGender()->shouldReturn(CustomerInterface::UNKNOWN_GENDER);
    }

    function it_can_check_if_gender_is_female(): void
    {
        $this->setGender(CustomerInterface::FEMALE_GENDER);
        $this->isFemale()->shouldReturn(true);
        $this->isMale()->shouldReturn(false);
    }

    function it_can_check_if_gender_is_male(): void
    {
        $this->setGender(CustomerInterface::MALE_GENDER);
        $this->isFemale()->shouldReturn(false);
        $this->isMale()->shouldReturn(true);
    }

    function it_has_no_group_by_default(): void
    {
        $this->getGroup()->shouldReturn(null);
    }

    function its_group_is_mutable(CustomerGroupInterface $group): void
    {
        $this->setGroup($group);
        $this->getGroup()->shouldReturn($group);
    }
}
