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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Mink\Session;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class ShopSecurityContext implements Context
{
    private SharedStorageInterface $sharedStorage;

    private SecurityServiceInterface $securityService;

    private ExampleFactoryInterface $userFactory;

    private UserRepositoryInterface $userRepository;

    private ?Session $session;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        ExampleFactoryInterface $userFactory,
        UserRepositoryInterface $userRepository,
        Session $session = null
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->session = $session;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs(string $email): void
    {
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }

    /**
     * @Given I am a logged in customer with name :fullName
     * @Given I am a logged in customer
     * @Given the customer logged in
     */
    public function iAmLoggedInCustomer(?string $fullName = null): void
    {
        $userData = ['email' => 'sylius@example.com', 'password' => 'sylius', 'enabled' => true];

        if ($fullName !== null) {
            $names = explode(' ', $fullName);

            $userData['first_name'] = $names[0];
            $userData['last_name'] = $names[1];
        }

        $user = $this->userFactory->create($userData);
        $this->userRepository->add($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }

    /**
     * @When Logged in user with email :email starts a new session
     */
    public function loggedInUserWithEmailStartsANewSession(string $email): void
    {
        $this->session->restart();

        $this->iAmLoggedInAs($email);
    }
}
