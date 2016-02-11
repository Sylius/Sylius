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
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\MinkAwareContext;
use Sylius\Behat\Page\Shop\HomePage;
use Sylius\Bundle\CoreBundle\Test\Services\SecurityServiceInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class SecurityContext implements Context, MinkAwareContext
{
    /**
     * @var SecurityServiceInterface
     */
    private $securityService;

    /**
     * @var HomePage
     */
    private $homePage;

    /**
     * @var Mink
     */
    private $mink;

    /**
     * @var array
     */
    private $minkParameters;

    /**
     * @param SecurityServiceInterface $securityService
     * @param HomePage $homePage
     */
    public function __construct(SecurityServiceInterface $securityService, HomePage $homePage)
    {
        $this->securityService = $securityService;
        $this->homePage = $homePage;
    }

    /**
     * @Given I am logged in as :email
     */
    public function iAmLoggedInAs($email)
    {
        $this->prepareSessionIfNeeded();
        $this->securityService->logIn($email, 'main', $this->mink->getSession());
    }

    /**
     * {@inheritdoc}
     */
    public function setMink(Mink $mink)
    {
        $this->mink = $mink;
    }

    /**
     * {@inheritdoc}
     */
    public function setMinkParameters(array $parameters)
    {
        $this->minkParameters = $parameters;
    }

    private function prepareSessionIfNeeded()
    {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            return;
        }

        if (false !== strpos($this->getSession()->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return;
        }

        $this->homePage->open();
    }

    /**
     * @param null $name
     *
     * @return Session
     */
    private function getSession($name = null)
    {
        return $this->mink->getSession($name);
    }
}
