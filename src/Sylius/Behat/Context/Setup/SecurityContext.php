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
use Sylius\Bundle\CoreBundle\Test\Services\SecurityServiceInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;

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
     * @var SharedStorageInterface
     */
    private $sharedStorage;

    /**
     * @param SecurityServiceInterface $securityService
     * @param Session $minkSession
     * @param array $minkParameters
     * @param HomePage $homePage
     * @param SharedStorageInterface $sharedStorage
     */
    public function __construct(
        SecurityServiceInterface $securityService,
        Session $minkSession,
        array $minkParameters,
        HomePage $homePage,
        SharedStorageInterface $sharedStorage
    ) {
        $this->securityService = $securityService;
        $this->minkSession = $minkSession;
        $this->minkParameters = $minkParameters;
        $this->homePage = $homePage;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->prepareSessionIfNeeded();
        $this->securityService->logIn($email, 'main', $this->minkSession);
    }

    /**
     * @Given /^I am logged in customer$/
     */
    public function iAmLoggedInCustomer()
    {
        $user = $this->securityService->logInDefaultUser($this->minkSession);

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
