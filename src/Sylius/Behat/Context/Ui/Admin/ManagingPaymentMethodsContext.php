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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPageInterface;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\Context\Ui\Admin\Helper\ValidationTrait;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\CreatePageInterface;
use Sylius\Behat\Page\Admin\PaymentMethod\UpdatePageInterface;
use Sylius\Behat\Service\Resolver\CurrentPageResolverInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final readonly class ManagingPaymentMethodsContext implements Context
{
    use ValidationTrait;

    /**
     * @param string[] $gatewayFactories
     */
    public function __construct(
        private CreatePageInterface $createPage,
        private IndexPageInterface $indexPage,
        private UpdatePageInterface $updatePage,
        private CurrentPageResolverInterface $currentPageResolver,
        private array $gatewayFactories,
    ) {
    }

    /**
     * @When I want to modify the :paymentMethod payment method
     *
     * @throws UnexpectedPageException
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
    public function iNameItIn(string $language, ?string $name = null): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        $currentPage->nameIt($name ?? '', $language);
    }

    /**
     * @When I update its :field with :value
     */
    public function iUpdateItsWith(string $field, string $value): void
    {
        match ($field) {
            'Publishable key' => $this->updatePage->setStripePublishableKey($value),
            'Secret key' => $this->updatePage->setStripeSecretKey($value),
            'Username' => $this->updatePage->setPaypalGatewayUsername($value),
            'Password' => $this->updatePage->setPaypalGatewayPassword($value),
            'Signature' => $this->updatePage->setPaypalGatewaySignature($value),
            default => throw new \InvalidArgumentException(sprintf('There is no configuration for "%s" field.', $field)),
        };
    }

    /**
     * @When I configure it with( only) :element
     */
    public function iConfigureItWith(string $element): void
    {
        match ($element) {
            'Publishable key' => $this->createPage->setStripePublishableKey('TEST'),
            'Secret key' => $this->createPage->setStripeSecretKey('TEST'),
            default => throw new \InvalidArgumentException(sprintf('There is no configuration for "%s" element.', $element)),
        };
    }

    /**
     * @When /^I set its "Username" as "([^"]+)", "Password" as "([^"]+)" and "Signature" as "([^"]+)"$/
     */
    public function iSetItsUsernameAsPasswordAsAndSignatureAs(string $username, string $password, string $signature): void
    {
        $this->updatePage->setPaypalGatewayUsername($username);
        $this->updatePage->setPaypalGatewayPassword($password);
        $this->updatePage->setPaypalGatewaySignature($signature);
    }

    /**
     * @When /^I set its "Publishable key" as "([^"]+)" and "Secret key" as "([^"]+)"$/
     */
    public function iSetItsPublishableKeyAsAndSecretKeyAs(string $publishableKey, string $secretKey): void
    {
        $this->updatePage->setStripePublishableKey($publishableKey);
        $this->updatePage->setStripeSecretKey($secretKey);
    }

    /**
     * @When I enable sandbox mode
     */
    public function iEnableSandboxMode(): void
    {
        $this->updatePage->enableSandboxMode();
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
     * @Then /^its gateway configuration "([^"]+)" should be "([^"]+)"$/
     */
    public function itsGatewayConfigurationShouldBe(string $element, string $value): void
    {
        Assert::true(
            $this->updatePage->hasResourceValues([StringInflector::nameToLowercaseCode($element) => $value]),
            sprintf('Expected "%s" to be "%s", but it is not.', StringInflector::nameToLowercaseCode($element), $value),
        );
    }

    /**
     * @Then this payment method should be in sandbox mode
     */
    public function thisPaymentMethodShouldBeInSandboxMode(): void
    {
        Assert::true($this->updatePage->isPaymentMethodInSandboxMode());
    }

    /**
     * @When I want to create a new offline payment method
     * @When I want to create a new payment method with :factory gateway factory
     *
     * @throws UnexpectedPageException
     */
    public function iWantToCreateANewPaymentMethod(string $factory = 'Offline'): void
    {
        $this->createPage->open(['factory' => array_search($factory, $this->gatewayFactories, true)]);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->createPage->specifyCode($code ?? '');
    }

    /**
     * @When I describe it as :description in :language
     */
    public function iDescribeItAsIn(string $description, string $language): void
    {
        $this->createPage->describeIt($description, $language);
    }

    /**
     * @When make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(string $channel): void
    {
        $this->createPage->checkChannel($channel);
    }

    /**
     * @Given I set its instruction as :instructions in :language
     */
    public function iSetItsInstructionAsIn(string $instructions, string $language): void
    {
        $this->createPage->setInstructions($instructions, $language);
    }

    /**
     * @When I add it
     * @When I try to add it
     *
     * @throws ElementNotFoundException
     */
    public function iAddIt(): void
    {
        $this->createPage->create();
    }

    /**
     * @When I cancel my changes
     */
    public function iCancelMyChanges(): void
    {
        $this->createPage->cancelChanges();
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
     *
     * @throws UnexpectedPageException
     */
    public function thePaymentMethodShouldAppearInTheRegistry(string $paymentMethodName): void
    {
        $this->indexPage->open();

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $paymentMethodName]));
    }

    /**
     * @Given /^(this payment method) should still be in the registry$/
     *
     * @throws UnexpectedPageException
     */
    public function thisPaymentMethodShouldStillBeInTheRegistry(PaymentMethodInterface $paymentMethod): void
    {
        $this->thePaymentMethodShouldAppearInTheRegistry($paymentMethod->getName());
    }

    /**
     * @Given I am browsing payment methods
     * @When I browse payment methods
     *
     * @throws UnexpectedPageException
     */
    public function iBrowsePaymentMethods(): void
    {
        $this->indexPage->open();
    }

    /**
     * @When I choose enabled filter
     */
    public function iChooseEnabledFilter(): void
    {
        $this->indexPage->chooseEnabledFilter();
    }

    /**
     * @When I filter
     */
    public function iFilter(): void
    {
        $this->indexPage->filter();
    }

    /**
     * @Then the first payment method on the list should have :field :value
     */
    public function theFirstPaymentMethodOnTheListShouldHave(string $field, string $value): void
    {
        Assert::same($this->indexPage->getColumnFields($field)[0], $value);
    }

    /**
     * @Then the last payment method on the list should have :field :value
     */
    public function theLastPaymentMethodOnTheListShouldHave(string $field, string $value): void
    {
        $values = $this->indexPage->getColumnFields($field);

        Assert::same(end($values), $value);
    }

    /**
     * @When I switch the way payment methods are sorted by :field
     * @When I start sorting payment methods by :field
     * @When I switch the way payment methods are sorted to descending by :field
     * @Given the payment methods are already sorted by :field
     */
    public function iSortPaymentMethodsBy(string $field): void
    {
        $this->indexPage->sortBy($field);
    }

    /**
     * @Then I should see a single payment method in the list
     * @Then I should see :amount payment methods in the list
     */
    public function iShouldSeePaymentMethodsInTheList(int $amount = 1): void
    {
        Assert::same($this->indexPage->countItems(), $amount);
    }

    /**
     * @Then I should be notified that :element is required
     * @Then I should be notified that I have to specify payment method :element
     *
     * @throws ElementNotFoundException
     */
    public function iShouldBeNotifiedThatIsRequired(string $element): void
    {
        $this->assertFieldValidationMessage($element, sprintf('Please enter payment method %s.', $element));
    }

    /**
     * @Then I should be notified that I have to specify paypal :element
     *
     * @throws ElementNotFoundException
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyPaypal(string $element): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('paypal_' . $element),
            sprintf('Please enter paypal %s.', $element),
        );
    }

    /**
     * @Then I should be notified that I have to specify stripe :element
     *
     * @throws ElementNotFoundException
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyStripe(string $element): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('stripe_' . str_replace(' ', '_', strtolower($element))),
            sprintf('Please enter stripe %s.', strtolower($element)),
        );
    }

    /**
     * @Then I should be notified that gateway name should contain only letters and underscores
     *
     * @throws ElementNotFoundException
     */
    public function iShouldBeNotifiedThatGatewayNameShouldContainOnlyLettersAndUnderscores(): void
    {
        Assert::same(
            $this->createPage->getValidationMessage('gateway_name'),
            'Gateway name should contain only letters and underscores.',
        );
    }

    /**
     * @Then the payment method with :element :value should not be added
     *
     * @throws UnexpectedPageException
     */
    public function thePaymentMethodWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        $this->iBrowsePaymentMethods();

        Assert::false($this->indexPage->isSingleResourceOnPage([$element => $value]));
    }

    /**
     * @Then /^(this payment method) should still be named "([^"]+)"$/
     *
     * @throws UnexpectedPageException
     */
    public function thisShippingMethodNameShouldBe(PaymentMethodInterface $paymentMethod, string $paymentMethodName): void
    {
        $this->iBrowsePaymentMethods();

        Assert::true($this->indexPage->isSingleResourceOnPage([
            'code' => $paymentMethod->getCode(),
            'name' => $paymentMethodName,
        ]));
    }

    /**
     * @throws ElementNotFoundException
     */
    private function assertFieldValidationMessage(string $element, string $expectedMessage): void
    {
        /** @var CreatePageInterface|UpdatePageInterface $currentPage */
        $currentPage = $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);

        Assert::same($currentPage->getValidationMessage($element), $expectedMessage);
    }

    /**
     * @Then the code field should be disabled
     * @Then I should not be able to edit its code
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
     *
     * @throws UnexpectedPageException
     */
    public function thePaymentMethodShouldHaveInstructionsIn(
        PaymentMethodInterface $paymentMethod,
        string $instructions,
        string $language,
    ): void {
        $this->iWantToModifyAPaymentMethod($paymentMethod);

        Assert::same($this->updatePage->getPaymentMethodInstructions($language), $instructions);
    }

    /**
     * @Then the payment method :paymentMethod should be available in channel :channelName
     *
     * @throws UnexpectedPageException
     */
    public function thePaymentMethodShouldBeAvailableInChannel(
        PaymentMethodInterface $paymentMethod,
        string $channelName,
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
     *
     * @throws ElementNotFoundException
     */
    public function iShouldBeNotifiedThatPaymentMethodWithThisCodeAlreadyExists(): void
    {
        Assert::same($this->createPage->getValidationMessage('code'), 'The payment method with given code already exists.');
    }

    /**
     * @Then there should still be only one payment method with :element :code
     *
     * @throws UnexpectedPageException
     */
    public function thereShouldStillBeOnlyOnePaymentMethodWith(string $element, string $code): void
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
    public function iConfigureItForUsernameWithSignature(string $username, string $signature): void
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



    /**
     * @Then I should be redirected to the previous page of only enabled payment methods
     */
    public function iShouldBeRedirectedToThePreviousFilteredPageWithFilter(): void
    {
        Assert::true($this->indexPage->isEnabledFilterApplied());
    }

    protected function resolveCurrentPage(): SymfonyPageInterface
    {
        return $this->currentPageResolver->getCurrentPageWithForm([$this->createPage, $this->updatePage]);
    }
}
