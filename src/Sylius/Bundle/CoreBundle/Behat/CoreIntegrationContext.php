<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

class CoreIntegrationContext extends DefaultContext
{
    /**
     * @var array[]
     */
    private $listeners;

    /**
     * @var mixed
     */
    private $service;

    /**
     * @Given /^I get the service "([^""]*)" from the Kernel container$/
     */
    function iGetTheService($service)
    {
        $this->service = $this->kernel->getContainer()->get($service);
    }

    /**
     * @Then /^I should get an instance of "([^""]*)"$/
     */
    function serviceIsInstanceOf($serviceName)
    {
        \PHPUnit_Framework_Assert::assertEquals($serviceName, get_class($this->service));
    }

    /**
     * @When /^I dispatch an event with "([^""]*)"$/
     */
    function iDispatchAnEvent($eventName) {
        $dispatcher = $this->kernel->getContainer()->get('event_dispatcher');

        \PHPUnit_Framework_Assert::assertTrue($dispatcher->hasListeners($eventName),
            sprintf('Event %s has not been dispatched by any listener', $eventName)
        );

        $this->listeners = $dispatcher->getListeners($eventName);
    }

    /**
     * @Then /^I should see "([^""]*)" with "([^""]*)" called$/
     */
    function iShouldSeeDispatcherCalled($eventDispatcher, $methodName) {

        foreach ($this->listeners as $listener) {
            if ($eventDispatcher === get_class($listener[0])) {
                \PHPUnit_Framework_Assert::assertEquals($listener[1], $methodName);
            }
        }
    }

}
