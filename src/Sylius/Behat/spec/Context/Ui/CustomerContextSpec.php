<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Ui;

use Behat\Mink\Mink;
use Behat\Mink\WebAssert;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use Sylius\Behat\Context\FeatureContext;
use Sylius\Behat\Page\Cart\CartSummaryPage;

/**
 * @author Magdalena Banasiak <magdalena.banasiak@lakion.com>
 */
class CustomerContextSpec extends ObjectBehavior
{
    function let(Factory $pageObjectFactory, Mink $mink)
    {
        $this->setPageObjectFactory($pageObjectFactory);
        $this->setMink($mink);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Ui\CustomerContext');
    }

    function it_is_feature_context()
    {
        $this->shouldHaveType(FeatureContext::class);
    }

    function it_checks_if_cart_has_given_total($pageObjectFactory, $mink, CartSummaryPage $customersIndexPage, WebAssert $assert)
    {
        $pageObjectFactory->createPage('Customer\CustomersIndexPage')->willReturn($customersIndexPage);
        $customersIndexPage->open()->shouldBeCalled();

        $mink->assertSession(null)->willReturn($assert);
        $assert->elementTextContains('css', 'data-customer', Argument::type('string'))->shouldBeCalled();
    }
}
