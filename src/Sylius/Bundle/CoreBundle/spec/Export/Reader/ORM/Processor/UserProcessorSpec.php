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
        \DateTime $lastLoginDate,
        \DateTime $passwordRequestedAtDate,
        \DateTime $expiresAtDate,
        \DateTime $credentialsExpireAtDate,
        \DateTime $createdAtDate,
        \DateTime $updatedAtDate,
        \DateTime $deletedAtDate
    )
    {
        $usersToConvert = array(
            array(
                'username' => 'John',
                'usernameCanonical' => 'John',
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
                'createdAt' => $createdAtDate,
                'updatedAt' => $updatedAtDate,
                'deletedAt' => $deletedAtDate,
                'id' => 1,
            ),
        );

        $outputUsers = array(
            array(
                'username' => 'John',
                'usernameCanonical' => 'John',
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
            ),
        );

        $dateConverter->toString(Argument::type('DateTime'), 'format')->shouldBeCalled()->willReturn('converted');

        $this->convert($usersToConvert, 'format')->shouldReturn($outputUsers);
    }
}