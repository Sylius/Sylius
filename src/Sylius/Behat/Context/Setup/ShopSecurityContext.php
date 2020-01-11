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
use Sylius\Behat\Service\SecurityServiceInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

final class ShopSecurityContext implements Context
{
    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var SecurityServiceInterface */
    private $securityService;

    /** @var ExampleFactoryInterface */
    private $userFactory;

    /** @var UserRepositoryInterface */
    private $userRepository;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        SecurityServiceInterface $securityService,
        ExampleFactoryInterface $userFactory,
        UserRepositoryInterface $userRepository
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->securityService = $securityService;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $user = $this->userRepository->findOneByEmail($email);
        Assert::notNull($user);

        $this->securityService->logIn($user);
    }

    /**
     * @Given I am a logged in customer
     */
    public function iAmLoggedInCustomer()
    {
        $user = $this->userFactory->create(['email' => 'sylius@example.com', 'password' => 'sylius', 'enabled' => true]);
        $this->userRepository->add($user);

        $this->securityService->logIn($user);

        $this->sharedStorage->set('user', $user);
    }
}
