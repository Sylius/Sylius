<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
final class UserContext implements Context
{
    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * @var ExampleFactoryInterface
     */
    private $userFactory;

    /**
     * @var ObjectManager
     */
    private $userManager;

    /**
     * @param SharedStorageInterface $sharedStorage
     * @param UserRepositoryInterface $userRepository
     * @param ExampleFactoryInterface $userFactory
     * @param ObjectManager $userManager
     */
    public function __construct(
        SharedStorageInterface $sharedStorage,
        UserRepositoryInterface $userRepository,
        ExampleFactoryInterface $userFactory,
        ObjectManager $userManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
    }

    /**
     * @Given there is a user :email identified by :password
     * @Given there was account of :email with password :password
     * @Given there is a user :email
     */
    public function thereIsUserIdentifiedBy($email, $password = 'sylius')
    {
        $user = $this->userFactory->create(['email' => $email, 'password' => $password, 'enabled' => true]);

        $this->sharedStorage->set('user', $user);

        $this->userRepository->add($user);
    }

    /**
     * @Given the account of :email was deleted
     * @Given my account :email was deleted
     */
    public function accountWasDeleted($email)
    {
        $user = $this->userRepository->findOneByEmail($email);

        $this->sharedStorage->set('customer', $user->getCustomer());

        $this->userRepository->remove($user);
    }

    /**
     * @Given its account was deleted
     */
    public function hisAccountWasDeleted()
    {
        $user = $this->sharedStorage->get('user');

        $this->userRepository->remove($user);
    }

    /**
     * @Given /^(this user) is not verified$/
     * @Given /^(I) have not verified my account (?:yet)$/
     */
    public function accountIsNotVerified(UserInterface $user)
    {
        $user->setVerifiedAt(null);

        $this->userManager->flush();
    }

    /**
     * @Given /^(?:(I) have|(this user) has) already received a verification email$/
     */
    public function iHaveReceivedVerificationEmail(UserInterface $user)
    {
        $this->prepareUserVerification($user);
    }

    /**
     * @Given a verification email has already been sent to :email
     */
    public function aVerificationEmailHasBeenSentTo($email)
    {
        $user = $this->userRepository->findOneByEmail($email);

        $this->prepareUserVerification($user);
    }

    /**
     * @Given /^(I) have already verified my account$/
     */
    public function iHaveAlreadyVerifiedMyAccount(UserInterface $user)
    {
        $user->setVerifiedAt(new \DateTime());

        $this->userManager->flush();
    }

    /**
     * @param UserInterface $user
     */
    private function prepareUserVerification(UserInterface $user)
    {
        $token = 'marryhadalittlelamb';
        $this->sharedStorage->set('verification_token', $token);

        $user->setEmailVerificationToken($token);

        $this->userManager->flush();
    }
}
