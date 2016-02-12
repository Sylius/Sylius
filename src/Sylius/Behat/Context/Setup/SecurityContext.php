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
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Session;
use Sylius\Behat\Page\Shop\HomePage;
use Sylius\Bundle\CoreBundle\Test\Factory\TestUserFactoryInterface;
use Sylius\Bundle\CoreBundle\Test\Services\SecurityServiceInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SecurityContext implements Context
{
    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var Session
     */
    private $minkSession;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @var HomePage
     */
    private $homePage;

    /**
     * @var TestUserFactoryInterface
     */
    private $testUserFactory;

    /**
     * @var RepositoryInterface
     */
    private $userRepository;

    /**
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SecurityServiceInterface $securityService
     * @param Session $minkSession
     * @param array $minkParameters
     * @param HomePage $homePage
     * @param TestUserFactoryInterface $testUserFactory
     * @param RepositoryInterface $userRepository
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SecurityServiceInterface $securityService,
        Session $minkSession,
        array $minkParameters,
        HomePage $homePage,
        TestUserFactoryInterface $testUserFactory,
        RepositoryInterface $userRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->securityService = $securityService;
        $this->minkSession = $minkSession;
        $this->minkParameters = $minkParameters;
        $this->homePage = $homePage;
        $this->testUserFactory = $testUserFactory;
        $this->userRepository = $userRepository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->prepareSessionIfNeeded();
        $this->securityService->logIn($email, $this->minkSession, 'main');
    }

    /**
     * @Given /^I am logged in customer$/
     */
    public function iAmLoggedInCustomer()
    {
        $user = $this->testUserFactory->createDefault();
        $this->userRepository->add($user);

        $this->securityService->logIn($user->getEmail(), $this->minkSession, 'main');

        $this->sharedStorage->setCurrentResource('user', $user);
    }

    private function prepareSessionIfNeeded()
    {
        if (!$this->minkSession->getDriver() instanceof Selenium2Driver) {
            return;
        }

        if (false !== strpos($this->minkSession->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return;
        }

        $this->homePage->open();
    }
}
