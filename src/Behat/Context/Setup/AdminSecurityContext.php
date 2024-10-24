<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class AdminSecurityContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private SecurityServiceInterface $securityService,
        private ExampleFactoryInterface $userFactory,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @Given I am logged in as an administrator
     * @Given there is logged in the administrator
     */
    public function iAmLoggedInAsAnAdministrator()
    {
        $user = $this->userFactory->create(['email' => 'sylius@example.com', 'password' => 'sylius', 'api' => true]);
        $this->userRepository->add($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('administrator', $user);
    }

    /**
     * @Given /^I am logged in as "([^"]+)" administrator$/
     */
    public function iAmLoggedInAsAdministrator($email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('administrator', $user);
    }

    /**
     * @Given I have been logged out from administration
     */
    public function iHaveBeenLoggedOutFromAdministration()
    {
        $this->securityService->logOut();

        $this->sharedStorage->set('administrator', null);
    }
}
