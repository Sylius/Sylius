<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Import\Writer\ORM\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Converter\DateConverterInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserProcessorSpec extends ObjectBehavior
{
    function let(DateConverterInterface $dateConverter)
    {
        $this->beConstructedWith($dateConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\Processor\UserProcessor');
    }

    function it_implements_user_processor_interface()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Import\Writer\ORM\Processor\UserProcessorInterface');
    }

    function it_restores_dateTimes_and_arrays(
        $dateConverter,
        \DateTime $birthdayDate,
        \DateTime $createdAtDate,
        \DateTime $customerCreatedAtDate,
        \DateTime $customerDeletedAtDate,
        \DateTime $customerUpdatedAtDate,
        \DateTime $deletedAtDate,
        \DateTime $updatedAtDate
    )
    {
        $flatArray = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'password' => 'password',
                'roles' => '["ROLE_ADMIN"]',
                'createdAt' => '2015-08-24 09:37:51',
                'updatedAt' => '2015-08-25 09:37:51',
                'deletedAt' => '2015-08-26 09:37:51',
                'id' => 1,
                'customerEmail' => 'john.doe@example.com',
                'customerEmailCanonical' => 'john.doe@example.com',
                'customerFirstName' => 'John',
                'customerLastName' => 'Doe',
                'customerBirthday' => '2014-07-24 09:37:51',
                'customerGender' => 'u',
                'customerCreatedAt' => '2015-07-24 09:37:51',
                'customerUpdatedAt' => '2015-07-25 09:37:51',
                'customerDeletedAt' => '2015-07-26 09:37:51',
                'customerId' => 20,
                'customerCurrency' => 'EUR',
            ),
        );

        $restoredArray = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'password' => 'password',
                'roles' => array(
                    'ROLE_ADMIN'
                ),
                'createdAt' => $createdAtDate,
                'updatedAt' => $updatedAtDate,
                'deletedAt' => $deletedAtDate,
                'id' => 1,
                'customer' => array(
                    'email' => 'john.doe@example.com',
                    'emailCanonical' => 'john.doe@example.com',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'birthday' => $birthdayDate,
                    'gender' => 'u',
                    'createdAt' => $customerCreatedAtDate,
                    'updatedAt' => $customerUpdatedAtDate,
                    'deletedAt' => $customerDeletedAtDate,
                    'id' => 20,
                    'currency' => 'EUR',
                ),
            ),
        );

        $dateConverter->toDateTime('2015-08-24 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($createdAtDate);
        $dateConverter->toDateTime('2015-08-25 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($updatedAtDate);
        $dateConverter->toDateTime('2015-08-26 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($deletedAtDate);
        $dateConverter->toDateTime('2015-07-24 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($customerCreatedAtDate);
        $dateConverter->toDateTime('2015-07-25 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($customerUpdatedAtDate);
        $dateConverter->toDateTime('2015-07-26 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($customerDeletedAtDate);
        $dateConverter->toDateTime('2014-07-24 09:37:51', 'Y-m-d H:i:s')->shouldBeCalled()->willReturn($birthdayDate);

        $this->convert($flatArray, 'Y-m-d H:i:s')->shouldReturn($restoredArray);
    }
}