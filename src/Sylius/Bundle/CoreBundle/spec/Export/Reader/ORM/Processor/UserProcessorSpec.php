<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor;

use PhpSpec\ObjectBehavior;
use Sylius\Component\ImportExport\Converter\DateConverter;
use Prophecy\Argument;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserProcessorSpec extends ObjectBehavior
{
    function let(DateConverter $dateConverter)
    {
        $this->beConstructedWith($dateConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor\UserProcessor');
    }

    function it_implements_user_processor_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Export\Reader\ORM\Processor\UserProcessorInterface');
    }

    function it_process_array_of_users_to_writers_friendly_form(
        $dateConverter,
        \DateTime $birthdayDate,
        \DateTime $createdAtDate,
        \DateTime $credentialsExpireAtDate,
        \DateTime $customerCreatedAtDate,
        \DateTime $customerDeletedAtDate,
        \DateTime $customerUpdatedAtDate,
        \DateTime $deletedAtDate,
        \DateTime $expiresAtDate,
        \DateTime $lastLoginDate,
        \DateTime $passwordRequestedAtDate,
        \DateTime $updatedAtDate
    )
    {
        $usersToConvert = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'salt' => 'salt',
                'password' => 'password',
                'lastLogin' => $lastLoginDate,
                'confirmationToken' => 'confirmation',
                'passwordRequestedAt' => $passwordRequestedAtDate,
                'locked' => '',
                'expiresAt' => $expiresAtDate,
                'credentialsExpireAt' => $credentialsExpireAtDate,
                'roles' => array(
                    'ROLE_ADMIN'
                ),
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
                'createdAt' => $createdAtDate,
                'updatedAt' => $updatedAtDate,
                'deletedAt' => $deletedAtDate,
                'id' => 1,
            ),
        );

        $outputUsers = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'salt' => 'salt',
                'password' => 'password',
                'lastLogin' => 'converted',
                'confirmationToken' => 'confirmation',
                'passwordRequestedAt' => 'converted',
                'locked' => '',
                'expiresAt' => 'converted',
                'credentialsExpireAt' => 'converted',
                'roles' => '["ROLE_ADMIN"]',
                'createdAt' => 'converted',
                'updatedAt' => 'converted',
                'deletedAt' => 'converted',
                'id' => 1,
                'customerEmail' => 'john.doe@example.com',
                'customerEmailCanonical' => 'john.doe@example.com',
                'customerFirstName' => 'John',
                'customerLastName' => 'Doe',
                'customerBirthday' => 'converted',
                'customerGender' => 'u',
                'customerCreatedAt' => 'converted',
                'customerUpdatedAt' => 'converted',
                'customerDeletedAt' => 'converted',
                'customerId' => 20,
                'customerCurrency' => 'EUR',
            ),
        );

        $dateConverter->toString(Argument::type('DateTime'), 'format')->shouldBeCalled()->willReturn('converted');

        $this->convert($usersToConvert, 'format')->shouldReturn($outputUsers);
    }
}