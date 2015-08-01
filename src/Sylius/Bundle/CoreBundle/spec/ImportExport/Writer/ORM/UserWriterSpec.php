<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\ImportExport\Writer\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Bundle\CoreBundle\ImportExport\Processor\UserProcessorInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserWriterSpec extends ObjectBehavior
{
    function let(
        UserProcessorInterface $userProcessor,
        UserRepositoryInterface $userRepository,
        RepositoryInterface $customerRepository,
        ObjectManager $entityManager
    )
    {
        $this->beConstructedWith(
            $userProcessor,
            $userRepository,
            $customerRepository,
            $entityManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\ImportExport\Writer\ORM\UserWriter');
    }

    function it_implements_import_export_writer_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Writer\WriterInterface');
    }

    function it_has_result_code()
    {
        $this->getResultCode()->shouldReturn(0);
    }

    function it_has_type()
    {
        $this->getType()->shouldReturn('user_orm');
    }

    function it_creates_new_users_from_user_array(
        $customerRepository,
        $entityManager,
        $userProcessor,
        $userRepository,
        \DateTime $date,
        CustomerInterface $customer,
        LoggerInterface $logger,
        UserInterface $user
    )
    {
        $flattenUsers = array(
            array(
                'User1'
            ),
        );

        $restoredUsers = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'plainPassword' => 'testPassword',
                'roles' => array(
                    'ROLE_USER'
                ),
                'createdAt' => $date,
                'updatedAt' => $date,
                'id' => 1,
                'customer' => array(
                    'email' => 'john.doe@example.com',
                    'emailCanonical' => 'john.doe@example.com',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'birthday' => $date,
                    'gender' => 'u',
                    'createdAt' => $date,
                    'updatedAt' => $date,
                    'deletedAt' => $date,
                    'id' => 20,
                    'currency' => 'EUR',
                ),
            ),
        );

        $userRepository->findOneByEmail('john.doe@example.com')->willReturn(null)->shouldBeCalled();
        $userRepository->createNew()->willReturn($user)->shouldBeCalled();
        $customerRepository->findOneBy(array('emailCanonical' => 'john.doe@example.com'))->willReturn(null)->shouldBeCalled();
        $customerRepository->createNew()->willReturn($customer)->shouldBeCalled();
        $user->getCustomer()->willReturn($customer)->shouldBeCalled();

        $userProcessor->revert($flattenUsers, 'format')->shouldBeCalled()->willReturn($restoredUsers);

        $user->setUsername('john.doe@example.com')->shouldBeCalled();
        $user->setUsernameCanonical('john.doe@example.com')->shouldBeCalled();
        $user->setEnabled(1)->shouldBeCalled();
        $user->setPlainPassword('testPassword')->shouldBeCalled();
        $user->setCreatedAt($date)->shouldBeCalled();
        $user->setUpdatedAt($date)->shouldBeCalled();
        $user->setRoles(array('ROLE_USER'))->shouldBeCalled();

        $user->setCustomer($customer)->shouldBeCalled();
        $customer->setUpdatedAt($date)->shouldBeCalled();
        $customer->setCreatedAt($date)->shouldBeCalled();
        $customer->setDeletedAt($date)->shouldBeCalled();
        $customer->setBirthday($date)->shouldBeCalled();
        $customer->setEmail('john.doe@example.com')->shouldBeCalled();
        $customer->setEmailCanonical('john.doe@example.com')->shouldBeCalled();
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();
        $customer->setGender('u')->shouldBeCalled();
        $customer->setCurrency('EUR')->shouldBeCalled();

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();
        $user->setConfirmationToken(Argument::any())->shouldNotBeCalled();
        $user->setPasswordRequestedAt(Argument::any())->shouldNotBeCalled();
        $user->setLocked(Argument::type('bool'))->shouldNotBeCalled();
        $user->setDeletedAt(Argument::any())->shouldNotBeCalled();

        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->write($flattenUsers, array('date_format' => 'format'), $logger);
    }

    function it_creates_new_users_for_defined_customer_from_user_array(
        $customerRepository,
        $entityManager,
        $userProcessor,
        $userRepository,
        \DateTime $date,
        CustomerInterface $customer,
        LoggerInterface $logger,
        UserInterface $user
    )
    {
        $flattenUsers = array(
            array(
                'User1'
            ),
        );

        $restoredUsers = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'plainPassword' => 'testPassword',
                'roles' => array(
                    'ROLE_USER'
                ),
                'createdAt' => $date,
                'updatedAt' => $date,
                'id' => 1,
                'customer' => array(
                    'emailCanonical' => 'john.doe@example.com',
                ),
            ),
        );

        $userRepository->findOneByEmail('john.doe@example.com')->willReturn(null)->shouldBeCalled();
        $userRepository->createNew()->willReturn($user)->shouldBeCalled();
        $customerRepository->findOneBy(array('emailCanonical' => 'john.doe@example.com'))->willReturn($customer)->shouldBeCalled();
        $user->getCustomer()->willReturn($customer)->shouldBeCalled();

        $userProcessor->revert($flattenUsers, 'format')->shouldBeCalled()->willReturn($restoredUsers);

        $user->setUsername('john.doe@example.com')->shouldBeCalled();
        $user->setUsernameCanonical('john.doe@example.com')->shouldBeCalled();
        $user->setEnabled(1)->shouldBeCalled();
        $user->setPlainPassword('testPassword')->shouldBeCalled();
        $user->setCreatedAt($date)->shouldBeCalled();
        $user->setUpdatedAt($date)->shouldBeCalled();
        $user->setRoles(array('ROLE_USER'))->shouldBeCalled();

        $user->setCustomer($customer)->shouldBeCalled();

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();
        $user->setConfirmationToken(Argument::any())->shouldNotBeCalled();
        $user->setPasswordRequestedAt(Argument::any())->shouldNotBeCalled();
        $user->setLocked(Argument::type('bool'))->shouldNotBeCalled();
        $user->setDeletedAt(Argument::any())->shouldNotBeCalled();

        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->write($flattenUsers, array('date_format' => 'format'), $logger);
    }

    function it_updates_existing_users_from_user_array(
        $entityManager,
        $userProcessor,
        $userRepository,
        \DateTime $date,
        CustomerInterface $customer,
        LoggerInterface $logger,
        UserInterface $user
    )
    {
        $flattenUsers = array(
            array(
                'User1'
            ),
        );

        $restoredUsers = array(
            array(
                'username' => 'john.doe@example.com',
                'usernameCanonical' => 'john.doe@example.com',
                'enabled' => 1,
                'plainPassword' => 'testPassword',
                'roles' => array(
                    'ROLE_USER'
                ),
                'createdAt' => $date,
                'updatedAt' => $date,
                'id' => 1,
                'customer' => array(
                    'email' => 'john.doe@example.com',
                    'emailCanonical' => 'john.doe@example.com',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                    'birthday' => $date,
                    'gender' => 'u',
                    'createdAt' => $date,
                    'updatedAt' => $date,
                    'deletedAt' => $date,
                    'id' => 20,
                    'currency' => 'EUR',
                ),
            ),
        );

        $userRepository->findOneByEmail('john.doe@example.com')->willReturn($user)->shouldBeCalled();
        $user->getCustomer()->willReturn($customer)->shouldBeCalled();

        $userProcessor->revert($flattenUsers, 'format')->shouldBeCalled()->willReturn($restoredUsers);

        $user->setUsername('john.doe@example.com')->shouldBeCalled();
        $user->setUsernameCanonical('john.doe@example.com')->shouldBeCalled();
        $user->setEnabled(1)->shouldBeCalled();
        $user->setPlainPassword('testPassword')->shouldBeCalled();
        $user->setCreatedAt($date)->shouldBeCalled();
        $user->setUpdatedAt($date)->shouldBeCalled();
        $user->setRoles(array('ROLE_USER'))->shouldBeCalled();

        $user->setCustomer($customer)->shouldBeCalled();
        $customer->setUpdatedAt($date)->shouldBeCalled();
        $customer->setCreatedAt($date)->shouldBeCalled();
        $customer->setDeletedAt($date)->shouldBeCalled();
        $customer->setBirthday($date)->shouldBeCalled();
        $customer->setEmail('john.doe@example.com')->shouldBeCalled();
        $customer->setEmailCanonical('john.doe@example.com')->shouldBeCalled();
        $customer->setFirstName('John')->shouldBeCalled();
        $customer->setLastName('Doe')->shouldBeCalled();
        $customer->setGender('u')->shouldBeCalled();
        $customer->setCurrency('EUR')->shouldBeCalled();

        $user->setLastLogin(Argument::any())->shouldNotBeCalled();
        $user->setConfirmationToken(Argument::any())->shouldNotBeCalled();
        $user->setPasswordRequestedAt(Argument::any())->shouldNotBeCalled();
        $user->setLocked(Argument::type('bool'))->shouldNotBeCalled();
        $user->setDeletedAt(Argument::any())->shouldNotBeCalled();

        $entityManager->persist($user)->shouldBeCalled();
        $entityManager->flush()->shouldBeCalled();

        $this->write($flattenUsers, array('date_format' => 'format'), $logger);
    }

    function it_finalize_job(JobInterface $job)
    {
        $job->addMetadata(array('result_code' => 0));

        $this->finalize($job, array());
    }
}
