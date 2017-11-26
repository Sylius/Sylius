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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\UpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

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
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @var array
     */
    private $gatewayFactories;

    /**
     * @param IndexPageInterface $indexPage
     * @param UpdatePageInterface $updatePage
     * @param CurrentPageResolverInterface $currentPageResolver
     * @param NotificationCheckerInterface $notificationChecker
     * @param array $gatewayFactories
     */
    public function __construct(
        CreatePageInterface $createPage,
        IndexPageInterface $indexPage,
        UpdatePageInterface $updatePage,
        CurrentPageResolverInterface $currentPageResolver,
        NotificationCheckerInterface $notificationChecker,
        array $gatewayFactories
    ) {
        $this->createPage = $createPage;
        $this->indexPage = $indexPage;
        $this->updatePage = $updatePage;
        $this->currentPageResolver = $currentPageResolver;
        $this->notificationChecker = $notificationChecker;
        $this->gatewayFactories = $gatewayFactories;
    }

    /**
     * @Given I want to modify the :paymentMethod payment method
     */
    public function iWantToModifyAPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->updatePage->open(['id' => $paymentMethod->getId()]);
    }

    /**
     * @When I name it :name in :language
     * @When I rename it to :name in :language
     * @When I remove its name from :language translation
     */
    public function iNameItIn($name = null, $language): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->nameIt($name, $language);
    }

    /**
     * @When I do not name it
     */
    public function iDoNotNameIt(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I enable it
     */
    public function iEnableIt(): void
    {
        $this->updatePage->enable();
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->updatePage->disable();
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete the :paymentMethod payment method
     * @When I try to delete the :paymentMethod payment method
     */
    public function iDeletePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['code' => $paymentMethod->getCode(), 'name' => $paymentMethod->getName()]);
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        $this->notificationChecker->checkNotification('Cannot delete, the payment method is in use.', NotificationType::failure());
    }

    /**
     * @When I choose :gatewayName gateway
     */
    public function iChooseGateway($gatewayName): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->chooseGateway($gatewayName);
    }

    /**
     * @Then this payment method :element should be :value
     */
    public function thisPaymentMethodElementShouldBe($element, $value): void
    {
        Assert::true($this->updatePage->hasResourceValues([$element => $value]));
    }

    /**
     * @When I want to create a new offline payment method
     * @When I want to create a new payment method with :factory gateway factory
     */
    public function iWantToCreateANewPaymentMethod($factory = 'Offline'): void
    {
        $this->createPage->open(['factory' => array_search($factory, $this->gatewayFactories, true)]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs($code = null): void
    {
        $this->createPage->specifyCode($code);
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAsIn($description, $language): void
    {
        $this->createPage->describeIt($description, $language);
    }

    /**
     * @When make it available in channel :channel
     */
    public function iMakeItAvailableInChannel($channel): void
    {
        $this->createPage->checkChannel($channel);
    }

    /**
     * @Given I set its instruction as :instructions in :language
     */
    public function iSetItsInstructionAsIn($instructions, $language): void
    {
        $this->createPage->setInstructions($instructions, $language);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I check (also) the :paymentMethodName payment method
     */
    public function iCheckThePaymentMethod(string $paymentMethodName): void
    {
        $this->indexPage->checkResourceOnPage(['name' => $paymentMethodName]);
    }

    /**
     * @When I delete them
     */
    public function iDeleteThem(): void
    {
        $this->indexPage->bulkDelete();
    }

    /**
     * @Then the payment method :paymentMethodName should appear in the registry
     * @Then the payment method :paymentMethodName should be in the registry
     * @Then I should see the payment method :paymentMethodName in the list
     */
    public function thePaymentMethodShouldAppearInTheRegistry(string $paymentMethodName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $paymentMethodName]));
    }

    /**
     * @Given /^(this payment method) should still be in the registry$/
     */
    public function thisPaymentMethodShouldStillBeInTheRegistry(PaymentMethodInterface $paymentMethod): void
    {
        $this->thePaymentMethodShouldAppearInTheRegistry($paymentMethod->getName());
    }

    /**
     * @Given I am browsing payment methods
     * @When I browse payment methods
     */
    public function iBrowsePaymentMethods(): void
    {
        $this->indexPage->open();
    }

    /**
     * @Then the first payment method on the list should have :field :value
     */
    public function theFirstPaymentMethodOnTheListShouldHave($field, $value): void
    {
        Assert::same($this->indexPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then the last payment method on the list should have :field :value
     */
    public function theLastPaymentMethodOnTheListShouldHave($field, $value): void
    {
        $values = $this->indexPage->getColumnFields($field);

        Assert::same(end($values), $value);
    }

    /**
     * @When I switch the way payment methods are sorted by :field
     * @When I start sorting payment methods by :field
     * @Given the payment methods are already sorted by :field
     */
    public function iSortPaymentMethodsBy($field): void
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @Then I should see a single payment method in the list
     * @Then I should see :amount payment methods in the list
     */
    public function iShouldSeePaymentMethodsInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), (int) $amount);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatIsRequired($element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter payment method %s.', $element));
    }

    /**
     * @Then I should be notified that I have to specify paypal :element
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyPaypal($element): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('paypal_' . $element),
            sprintf('Please enter paypal %s.', $element)
        );
    }

    /**
     * @Then I should be notified that gateway name should contain only letters and underscores
     */
    public function iShouldBeNotifiedThatGatewayNameShouldContainOnlyLettersAndUnderscores(): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('gateway_name'),
            'Gateway name should contain only letters and underscores.'
        );
    }

    /**
     * @Then the payment method with :element :value should not be added
     */
    public function thePaymentMethodWithElementValueShouldNotBeAdded($element, $value): void
    {
        $this->iBrowsePaymentMethods();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^(this payment method) should still be named "([^"]+)"$/
     */
    public function thisShippingMethodNameShouldBe(PaymentMethodInterface $paymentMethod, $paymentMethodName): void
    {
        $this->iBrowsePaymentMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $paymentMethod->getCode(),
            'name' => $paymentMethodName,
        ]));
    }

    /**
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @Then the code field should be disabled
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isCodeDisabled());
    }

    /**
     * @Then the factory name field should be disabled
     */
    public function theFactoryNameFieldShouldBeDisabled(): void
    {
        Assert::true($this->updatePage->isFactoryNameFieldDisabled());
    }

    /**
     * @Then this payment method should be enabled
     */
    public function thisPaymentMethodShouldBeEnabled(): void
    {
        Assert::true($this->updatePage->isPaymentMethodEnabled());
    }

    /**
     * @Then this payment method should be disabled
     */
    public function thisPaymentMethodShouldBeDisabled(): void
    {
        Assert::false($this->updatePage->isPaymentMethodEnabled());
    }

    /**
     * @Given the payment method :paymentMethod should have instructions :instructions in :language
     */
    public function thePaymentMethodShouldHaveInstructionsIn(
        PaymentMethodInterface $paymentMethod,
        $instructions,
        $language
    ): void {
        $this->iWantToModifyAPaymentMethod($paymentMethod);

        Assert::same($this->updatePage->getPaymentMethodInstructions($language), $instructions);
    }

    /**
     * @Then the payment method :paymentMethod should be available in channel :channelName
     */
    public function thePaymentMethodShouldBeAvailableInChannel(
        PaymentMethodInterface $paymentMethod,
        $channelName
    ): void {
        $this->iWantToModifyAPaymentMethod($paymentMethod);

        Assert::true($this->updatePage->isAvailableInChannel($channelName));
    }

    /**
     * @Then /^(this payment method) should no longer exist in the registry$/
     */
    public function thisPaymentMethodShouldNoLongerExistInTheRegistry(PaymentMethodInterface $paymentMethod): void
    {
        Assert::false($this->indexPage->isSingleResourceOnPage([
            'code' => $paymentMethod->getCode(),
            'name' => $paymentMethod->getName(),
        ]));
    }

    /**
     * @Then I should be notified that payment method with this code already exists
     */
    public function iShouldBeNotifiedThatPaymentMethodWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The payment method with given code already exists.');
    }

    /**
     * @Then there should still be only one payment method with :element :code
     */
    public function thereShouldStillBeOnlyOnePaymentMethodWith($element, $code): void
    {
        $this->iBrowsePaymentMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([$element => $code]));
    }

    /**
     * @When I configure it with test paypal credentials
     */
    public function iConfigureItWithTestPaypalCredentials(): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setPaypalGatewayUsername('TEST');
        $currentPage->setPaypalGatewayPassword('TEST');
        $currentPage->setPaypalGatewaySignature('TEST');
    }

    /**
     * @When I configure it for username :username with :signature signature
     */
    public function iConfigureItForUsernameWithSignature($username, $signature): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->setPaypalGatewayUsername($username);
        $currentPage->setPaypalGatewaySignature($signature);
    }

    /**
     * @When I do not specify configuration password
     */
    public function iDoNotSpecifyConfigurationPassword(): void
    {
        // Intentionally left blank to fulfill context expectation
    }

    /**
     * @When I configure it with test stripe gateway data
     */
    public function iConfigureItWithTestStripeGatewayData(): void
    {
        $this->createPage->setStripeSecretKey('TEST');
        $this->createPage->setStripePublishableKey('TEST');
    }
}
