<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context;

use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\MinkAwareContext;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\WebAssert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class FeatureContext extends PageObjectContext implements MinkAwareContext
{
    /**
     * @var Mink
     */
    protected $mink;

    /**
     * @var array
     */
    protected $minkParameters;

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

    /**
     * @param string|null $name name of the session OR active session will be used
     *
     * @return Session
     */
    public function getSession($name = null)
    {
        return $this->mink->getSession($name);
    }

    /**
     * @param string|null $name name of the session OR active session will be used
     *
     * @return WebAssert
     */
    public function assertSession($name = null)
    {
        return $this->mink->assertSession($name);
    }

    protected function prepareSessionIfNeeded()
    {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            return;
        }

        if (false !== strpos($this->getSession()->getCurrentUrl(), $this->minkParameters['base_url'])) {
            return;
        }

        $this->getPage('Shop\HomePage')->open();
    }
}
