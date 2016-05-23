<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ManagingPaymentMethodsContext implements Context
{
    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param CreatePageInterface $createPage
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * @Given I want to modify the :paymentMethod payment method
     */
    public function iWantToModifyAPaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        $this->updatePage->open(['id' => $paymentMethod->getId()]);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iNameItIn($name = null, $language)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->nameIt($name, $language);
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt()
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I enable it
     */
    public function iEnableIt()
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt()
    {
        $this->updatePage->disable();
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the :paymentMethod payment method
     */
    public function iDeletePaymentMethod(PaymentMethodInterface $paymentMethod)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $paymentMethod->getCode(), 'name' => $paymentMethod->getName()]);
    }

    /**
     * @When I choose :gatewayName gateway
     */
    public function iChooseGateway($gatewayName)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseGateway($gatewayName);
    }

    /**
     * @Then this payment method :element should be :value
     */
    public function thisPaymentMethodElementShouldBe($element, $value)
    {
        Assert::true(
            $this->updatePage->hasResourceValues([$element => $value]),
            sprintf('Payment method %s should be %s', $element, $value)
        );
    }

    /**
     * @Given I want to create a new payment method
     */
    public function iWantToCreateANewPaymentMethod()
    {
        $this->createPage->open();
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null)
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAsIn($description, $language)
    {
        $this->createPage->describeIt($description, $language);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then the payment method :paymentMethodName should appear in the registry
     * @Then the payment method :paymentMethodName should be in the registry
     */
    public function thePaymentMethodShouldAppearInTheRegistry($paymentMethodName)
    {
        $this->indexPage->open();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage(['name' => $paymentMethodName]),
            sprintf('Payment method with name %s has not been found.', $paymentMethodName)
        );
    }

    /**
     * @When I browse payment methods
     */
    public function iBrowsePaymentMethods()
    {
        $this->indexPage->open();
    }

    /**
     * @Then I should see :amount payment methods in the list
     */
    public function iShouldSeePaymentMethodsInTheList($amount)
    {
        $foundRows = $this->indexPage->countItems();

        Assert::eq(
            ((int) $amount),
            $foundRows,
            '%2$s rows with payment methods should appear on page, %s rows has been found'
        );
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element)
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter payment method %s.', $element));
    }

    /**
     * @Then the payment method with :element :value should not be added
     */
    public function thePaymentMethodWithElementValueShouldNotBeAdded($element, $value)
    {
        $this->iBrowsePaymentMethods();

        Assert::false(
            $this->indexPage->isSingleResourceOnPage([$element => $value]),
            sprintf('Payment method with %s %s was created, but it should not.', $element, $value)
        );
    }

    /**
     * @Then /^(this payment method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(PaymentMethodInterface $paymentMethod, $paymentMethodName)
    {
        $this->iBrowsePaymentMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([
                'code' => $paymentMethod->getCode(),
                'name' => $paymentMethodName,
            ]),
            sprintf('Payment method name %s has not been assigned properly.', $paymentMethodName)
        );
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage($element, $expectedMessage)
    {
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::true(
            $currentPage->checkValidationMessageFor($element, $expectedMessage),
            sprintf('Payment method %s should be required.', $element)
        );
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled()
    {
        Assert::true(
            $this->updatePage->isCodeDisabled(),
            'Code field should be disabled'
        );
    }

    /**
     * @Then this payment method should be enabled
     */
    public function thisPaymentMethodShouldBeEnabled()
    {
        Assert::true(
            $this->updatePage->isPaymentMethodEnabled(),
            'Payment method should be enabled'
        );
    }

    /**
     * @Then this payment method should be disabled
     */
    public function thisPaymentMethodShouldBeDisabled()
    {
        Assert::false(
            $this->updatePage->isPaymentMethodEnabled(),
            'Payment method should be disabled'
        );
    }

    /**
     * @Then /^(this payment method) should no longer exist in the registry$/
     */
    public function thisPaymentMethodShouldNoLongerExistInTheRegistry(PaymentMethodInterface $paymentMethod)
    {
        Assert::false(
            $this->indexPage->isSingleResourceOnPage(['code' => $paymentMethod->getCode(), 'name' => $paymentMethod->getName()]),
            sprintf('Payment method %s should no longer exist in the registry', $paymentMethod->getName())
        );
    }

    /**
     * @Then I should be notified that payment method with this code already exists
     */
    public function iShouldBeNotifiedThatPaymentMethodWithThisCodeAlreadyExists()
    {
        Assert::true(
            $this->createPage->checkValidationMessageFor('code', 'The payment method with given code already exists.'),
            'Unique code violation message should appear on page, but it does not.'
        );
    }

    /**
     * @Then there should still be only one payment method with :element :code
     */
    public function thereShouldStillBeOnlyOnePaymentMethodWith($element, $code)
    {
        $this->iBrowsePaymentMethods();

        Assert::true(
            $this->indexPage->isSingleResourceOnPage([$element => $code]),
            sprintf('Payment method with %s %s cannot be found.', $element, $code)
        );
    }
}
