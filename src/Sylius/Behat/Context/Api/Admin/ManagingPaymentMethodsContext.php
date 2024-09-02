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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\Helper\ValidationTrait;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Service\Converter\SectionAwareIriConverter;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Webmozart\Assert\Assert;

final class ManagingPaymentMethodsContext implements Context
{
    use ValidationTrait;

    public const SORT_TYPES = ['ascending' => 'asc', 'descending' => 'desc'];

    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SectionAwareIriConverter $sectionAwareIriConverter,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to modify the :paymentMethod payment method
     */
    public function iWantToModifyAPaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->client->buildUpdateRequest(Resources::PAYMENT_METHODS, $paymentMethod->getCode());
    }

    /**
     * @When I name it :name in :localeCode
     * @When I rename it to :name in :localeCode
     * @When I remove its name from :localeCode translation
     */
    public function iNameItIn(string $localeCode, ?string $name = null): void
    {
        $this->client->addRequestData('translations', [$localeCode => ['name' => $name]]);
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
        $this->client->addRequestData('enabled', true);
    }

    /**
     * @When I disable it
     */
    public function iDisableIt(): void
    {
        $this->client->addRequestData('enabled', false);
    }

    /**
     * @When I delete the :paymentMethod payment method
     * @When I try to delete the :paymentMethod payment method
     */
    public function iDeletePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->client->delete(Resources::PAYMENT_METHODS, $paymentMethod->getCode());
    }

    /**
     * @When I want to create a new offline payment method
     * @When I want to create a new payment method with :factory gateway factory
     */
    public function iWantToCreateANewPaymentMethod(string $factory = 'Offline'): void
    {
        $factory = str_replace(' ', '_', strtolower($factory));

        $this->client->buildCreateRequest(Resources::PAYMENT_METHODS);
        $this->client->addRequestData('gatewayConfig', ['factoryName' => $factory, 'gatewayName' => $factory]);
    }

    /**
     * @When I want to create a new payment method without gateway configuration
     */
    public function iWantToCreateANewPaymentMethodWithoutGatewayConfiguration(): void
    {
        $this->client->buildCreateRequest(Resources::PAYMENT_METHODS);
        $this->client->addRequestData('code', 'TEST');
    }

    /**
     * @When I want to create a new payment method without gateway name
     */
    public function iWantToCreateANewPaymentMethodWithoutGatewayName(): void
    {
        $this->client->buildCreateRequest(Resources::PAYMENT_METHODS);
        $this->client->addRequestData('code', 'TEST');
        $this->client->addRequestData('gatewayConfig', ['factoryName' => 'offline']);
    }

    /**
     * @When I want to create a new payment method without factory name
     */
    public function iWantToCreateANewPaymentMethodWithoutFactoryName(): void
    {
        $this->client->buildCreateRequest(Resources::PAYMENT_METHODS);
        $this->client->addRequestData('code', 'TEST');
        $this->client->addRequestData('gatewayConfig', ['gatewayName' => 'offline']);
    }

    /**
     * @When I want to create a new payment method with wrong factory name
     */
    public function iWantToCreateANewPaymentMethodWithWrongFactoryName(): void
    {
        $this->client->buildCreateRequest(Resources::PAYMENT_METHODS);
        $this->client->addRequestData('code', 'TEST');
        $this->client->addRequestData('gatewayConfig', ['factoryName' => 'gateway_with_wrong_factory_name', 'gatewayName' => 'gateway with wrong factory name']);
    }

    /**
     * @When I specify its code as :code
     * @When I do not specify its code
     */
    public function iSpecifyItsCodeAs(?string $code = null): void
    {
        $this->client->addRequestData('code', $code);
    }

    /**
     * @When I describe it as :description in :localeCode
     */
    public function iDescribeItAsIn(string $description, string $localeCode): void
    {
        $this->client->addRequestData('translations', [$localeCode => ['description' => $description]]);
    }

    /**
     * @When make it available in channel :channel
     */
    public function iMakeItAvailableInChannel(ChannelInterface $channel): void
    {
        $this->client->replaceRequestData('channels', [$this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin')]);
    }

    /**
     * @When I set its instruction as :instructions in :localeCode
     */
    public function iSetItsInstructionAsIn(string $instructions, string $localeCode): void
    {
        $this->client->addRequestData('translations', [$localeCode => ['instructions' => $instructions]]);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt(): void
    {
        $this->client->create();
    }

    /**
     * @When I cancel my changes
     */
    public function iCancelMyChanges(): void
    {
        $this->createPage->cancelChanges();
    }

    /**
     * @When I start sorting payment methods by name
     * @When the payment methods are already sorted by name
     * @When I switch the way payment methods are sorted to :sortType by name
     */
    public function iSortShippingMethodsByName(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'translation.name' => self::SORT_TYPES[$sortType],
            'localeCode' => $this->getAdminLocaleCode(),
        ]);
    }

    /**
     * @Given the payment methods are already sorted by code
     * @When I start sorting payment methods by code
     * @When I switch the way payment methods are sorted to :sortType by code
     */
    public function iSortShippingMethodsByCode(string $sortType = 'ascending'): void
    {
        $this->client->sort([
            'code' => self::SORT_TYPES[$sortType],
            'localeCode' => $this->getAdminLocaleCode(),
        ]);
    }

    /**
     * @When I configure it with test paypal credentials
     */
    public function iConfigureItWithTestPaypalCredentials(): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'username' => 'test',
                    'password' => 'test',
                    'signature' => 'test',
                    'sandbox' => true,
                ],
            ],
        );
    }

    /**
     * @When I configure it for username :username with :signature signature
     */
    public function iConfigureItForUsernameWithSignature(string $username, string $signature): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'username' => $username,
                    'signature' => $signature,
                    'sandbox' => true,
                ],
            ],
        );
    }

    /**
     * @When I configure it for username :username with :signature signature and password, but without sandbox
     */
    public function iConfigureItForUsernameWithSignatureButWithoutSandbox(string $username, string $signature): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'username' => $username,
                    'signature' => $signature,
                    'password' => 'TEST',
                    'sandbox' => null,
                ],
            ],
        );
    }

    /**
     * @When I configure it for username :username with :signature signature and password, but with sandbox that has wrong type
     */
    public function iConfigureItForUsernameWithSignatureButWithWrongSandboxType(string $username, string $signature): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'username' => $username,
                    'signature' => $signature,
                    'password' => 'TEST',
                    'sandbox' => 'test',
                ],
            ],
        );
    }

    /**
     * @When I configure it with only :element
     */
    public function iConfigureItWithOnly(string $element): void
    {
        $element = str_replace(' ', '_', strtolower($element));

        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                   $element => 'TEST',
                   $element === 'secret_key' ? 'publishable_key' : 'secret_key' => null,
                ],
            ],
        );
    }

    /**
     * @When I do not specify configuration password
     */
    public function iDoNotSpecifyConfigurationPassword(): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'password' => null,
                ],
            ],
        );
    }

    /**
     * @When I configure it with test stripe gateway data
     */
    public function iConfigureItWithTestStripeGatewayData(): void
    {
        $this->client->addRequestData(
            'gatewayConfig',
            [
                'config' => [
                    'publishable_key' => 'test',
                    'secret_key' => 'test',
                ],
            ],
        );
    }

    /**
     * @Given I am browsing payment methods
     * @When I browse payment methods
     */
    public function iBrowsePaymentMethods(): void
    {
        $this->client->index(Resources::PAYMENT_METHODS);
    }

    /**
     * @When I change my locale to :localeCode
     */
    public function iChangeMyLocaleTo(string $localeCode): void
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $this->client->buildUpdateRequest(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        $this->client->updateRequestData(['localeCode' => $localeCode]);
        $this->client->update();
    }

    /**
     * @Then the first payment method on the list should have :field :value
     */
    public function theFirstPaymentMethodOnTheListShouldHave(string $field, string $value): void
    {
        $response = $this->client->getLastResponse();

        $paymentMethods = $this->responseChecker->getCollection($response);

        Assert::same($this->getFieldValueOfFirstPaymentMethod($paymentMethods[0], $field), $value);
    }

    /**
     * @Then the last payment method on the list should have :field :value
     */
    public function theLastPaymentMethodOnTheListShouldHave(string $field, string $value): void
    {
        $response = $this->client->index(Resources::PAYMENT_METHODS);

        if ($field = 'name') {
            $paymentMethods = $this->responseChecker->getCollection($response);

            Assert::same(end($paymentMethods)['translations']['en_US']['name'], $value);

            return;
        }

        $count = $this->responseChecker->countCollectionItems($response);

        Assert::true(
            $this->responseChecker->hasItemOnPositionWithValue($this->client->getLastResponse(), $count - 1, $field, $value),
            sprintf('There should be payment method with %s "%s" on position %d, but it does not.', $field, $value, $count - 1),
        );
    }

    /**
     * @Then I should see a single payment method in the list
     * @Then I should see :amount payment methods in the list
     */
    public function iShouldSeePaymentMethodsInTheList(int $amount = 1): void
    {
        Assert::same($this->responseChecker->countCollectionItems($this->client->getLastResponse()), $amount);
    }

    /**
     * @Then I should be notified that :element is required
     */
    public function iShouldBeNotifiedThatElementIsRequired(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('The type of the "%s" attribute must be "string", "NULL" given.', $element),
        );
    }

    /**
     * @Then I should be notified that I have to specify payment method :element
     */
    public function iShouldBeNotifiedThatINeedToSpecifyPaymentMethodName(string $element): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('%s: Please enter payment method %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that I have to specify paypal :element
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyPaypal(string $element): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('gatewayConfig.config[%s]: Please enter paypal %s.', $element, $element),
        );
    }

    /**
     * @Then I should be notified that I have to specify paypal sandbox status
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyPaypalSandboxStatus(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig.config[sandbox]: Please set your paypal sandbox status.',
        );
    }

    /**
     * @Then I should be notified that I have to specify paypal sandbox status that is boolean
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyPaypalSandboxStatusThatIsBoolean(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig.config[sandbox]: This value should be of type bool.',
        );
    }

    /**
     * @Then I should be notified that I have to specify stripe :element
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyStripe(string $element): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            sprintf('gatewayConfig.config[%s]: Please enter stripe %s.', str_replace(' ', '_', strtolower($element)), $element),
        );
    }

    /**
     * @Then I should be notified that I have to specify gateway configuration
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyGatewayConfiguration(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig: This value should not be blank.',
        );
    }

    /**
     * @Then I should be notified that I have to specify gateway name
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyGatewayName(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig.gatewayName: Please enter gateway name.',
        );
    }

    /**
     * @Then I should be notified that I have to specify factory name
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyFactoryName(): void
    {
        Assert::same(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig.factoryName: Please enter gateway factory name.',
        );
    }

    /**
     * @Then I should be notified that I have to specify factory name that is available
     */
    public function iShouldBeNotifiedThatIHaveToSpecifyFactoryNameThatIsAvailable(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'gatewayConfig.factoryName: Invalid gateway factory. Available factories are ',
        );
    }

    /**
     * @Then the payment method with :element :value should not be added
     */
    public function thePaymentMethodWithElementValueShouldNotBeAdded(string $element, string $value): void
    {
        if ($element === 'name') {
            Assert::false(
                in_array(
                    $value,
                    $this->getPaymentMethodNamesFromCollection(),
                ),
                sprintf('Payment method should have name "%s", but it does not', $value),
            );

            return;
        }

        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PAYMENT_METHODS), $element, $value),
            sprintf('Payment method with %s: %s exists', $element, $value),
        );
    }

    /**
     * @Then this payment method should still be named :paymentMethodName
     */
    public function thisPaymentMethodNameShouldStillBeNamed(string $paymentMethodName): void
    {
        Assert::inArray(
            $paymentMethodName,
            $this->getPaymentMethodNamesFromCollection(),
            sprintf('Payment method with name %s does not exist', $paymentMethodName),
        );
    }

    /**
     * @Then the code field should be disabled
     * @Then I should not be able to edit its code
     */
    public function theCodeFieldShouldBeDisabled(): void
    {
        $this->client->updateRequestData(['code' => 'NEW_CODE']);

        Assert::false($this->responseChecker->hasValue($this->client->update(), 'code', 'NEW_CODE'));
    }

    /**
     * @Then the factory name field should be disabled
     */
    public function theFactoryNameFieldShouldBeDisabled(): void
    {
        $paymentMethodCode = $this->responseChecker->getValue($this->client->getLastResponse(), 'code');

        $this->client->addRequestData('gatewayConfig', ['factoryName' => 'NEWFACTORYNAME']);
        $this->client->update();

        Assert::false($this->responseChecker->hasValue($this->client->customItemAction(Resources::PAYMENT_METHODS, $paymentMethodCode, HttpRequest::METHOD_GET, 'gateway-config'), 'factoryName', 'NEWFACTORYNAME'));
    }

    /**
     * @Then /^(this payment method) should be enabled/
     */
    public function thisPaymentMethodShouldBeEnabled(PaymentMethodInterface $paymentMethod): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::PAYMENT_METHODS, $paymentMethod->getCode()),
                'enabled',
                true,
            ),
            'This payment method should be enabled',
        );
    }

    /**
     * @Then /^(this payment method) should be disabled$/
     */
    public function thisShippingMethodShouldBeDisabled(PaymentMethodInterface $paymentMethod): void
    {
        Assert::true(
            $this->responseChecker->hasValue(
                $this->client->show(Resources::PAYMENT_METHODS, $paymentMethod->getCode()),
                'enabled',
                false,
            ),
            'This payment method should be disabled',
        );
    }

    /**
     * @Then the payment method :paymentMethod should have instructions :instructions in :localeCode
     */
    public function thePaymentMethodShouldHaveInstructionsIn(
        PaymentMethodInterface $paymentMethod,
        string $instructions,
        string $localeCode,
    ): void {
        $translations = $this->responseChecker->getValue($this->client->show(Resources::PAYMENT_METHODS, $paymentMethod->getCode()), 'translations');

        Assert::same(
            $translations[$localeCode]['instructions'],
            $instructions,
            sprintf('Payment method does not have %s instruction', $instructions),
        );
    }

    /**
     * @Then the payment method :paymentMethod should be available in channel :channel
     */
    public function thePaymentMethodShouldBeAvailableInChannel(
        PaymentMethodInterface $paymentMethod,
        ChannelInterface $channel,
    ): void {
        $this->client->show(Resources::PAYMENT_METHODS, $paymentMethod->getCode());
        $channelsArray = $this->responseChecker->getValue($this->client->getLastResponse(), 'channels');

        Assert::true(in_array($this->sectionAwareIriConverter->getIriFromResourceInSection($channel, 'admin'), $channelsArray));
    }

    /**
     * @Then /^(this payment method) should no longer exist in the registry$/
     */
    public function thisPaymentMethodShouldNoLongerExistInTheRegistry(PaymentMethodInterface $paymentMethod): void
    {
        Assert::false(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PAYMENT_METHODS), 'code', $paymentMethod->getCode()),
            sprintf('Payment method with code %s exists but should not', $paymentMethod->getCode()),
        );
    }

    /**
     * @Then I should be notified that payment method with this code already exists
     */
    public function iShouldBeNotifiedThatPaymentMethodWithThisCodeAlreadyExists(): void
    {
        $response = $this->client->getLastResponse();
        Assert::false(
            $this->responseChecker->isCreationSuccessful($response),
            'Payment method  has been created successfully, but it should not',
        );
        Assert::same(
            $this->responseChecker->getError($response),
            'code: The payment method with given code already exists.',
        );
    }

    /**
     * @Then there should still be only one payment method with :element :code
     */
    public function thereShouldStillBeOnlyOnePaymentMethodWith(string $element, string $code): void
    {
        $response = $this->client->index(Resources::PAYMENT_METHODS);
        $itemsCount = $this->responseChecker->countCollectionItems($response);

        Assert::same($itemsCount, 1, sprintf('Expected 1 payment method, but got %d', $itemsCount));
        Assert::true($this->responseChecker->hasItemWithValue($response, $element, $code));
    }

    /**
     * @Then this payment method :element should be :value
     */
    public function thisPaymentMethodElementShouldBe(
        string $element,
        string $value,
    ): void {
        if ($element === 'name') {
            Assert::inArray(
                $value,
                $this->getPaymentMethodNamesFromCollection(),
                sprintf('Payment method should have name "%s", but it does not', $value),
            );

            return;
        }

        Assert::true(
            $this->responseChecker->hasItemWithValue($this->client->index(Resources::PAYMENT_METHODS), $element, $value),
            sprintf('Payment method should have %s "%s", but it does,', $element, $value),
        );
    }

    /**
     * @Then I should be notified that it has been successfully created
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyCreated(): void
    {
        Assert::true(
            $this->responseChecker->isCreationSuccessful($this->client->getLastResponse()),
            'Payment method could not be created',
        );
    }

    /**
     * @Then I should be notified that it has been successfully deleted
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted(): void
    {
        Assert::true(
            $this->responseChecker->isDeletionSuccessful($this->client->getLastResponse()),
            'Payment method could not be deleted',
        );
    }

    /**
     * @Then I should be notified that it is in use
     */
    public function iShouldBeNotifiedThatItIsInUse(): void
    {
        Assert::contains(
            $this->responseChecker->getError($this->client->getLastResponse()),
            'Cannot remove, the payment method is in use.',
        );
    }

    /**
     * @Then the payment method :paymentMethodName should appear in the registry
     * @Then the payment method :paymentMethodName should be in the registry
     * @Then I should see the payment method :paymentMethodName in the list
     */
    public function thePaymentMethodShouldAppearInTheRegistry(string $paymentMethodName): void
    {
        Assert::inArray(
            $paymentMethodName,
            $this->getPaymentMethodNamesFromCollection(),
            sprintf('Payment method with name %s does not exist', $paymentMethodName),
        );
    }

    /**
     * @Then /^(this payment method) should still be in the registry$/
     */
    public function thisPaymentMethodShouldStillBeInTheRegistry(PaymentMethodInterface $paymentMethod): void
    {
        $this->thePaymentMethodShouldAppearInTheRegistry($paymentMethod->getName());
    }

    private function getAdminLocaleCode(): string
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->sharedStorage->get('administrator');

        $response = $this->client->show(Resources::ADMINISTRATORS, (string) $adminUser->getId());

        return $this->responseChecker->getValue($response, 'localeCode');
    }

    private function getFieldValueOfFirstPaymentMethod(array $paymentMethod, string $field): ?string
    {
        if ($field === 'code') {
            return $paymentMethod['code'];
        }

        if ($field === 'name') {
            return $paymentMethod['translations'][$this->getAdminLocaleCode()]['name'];
        }

        return null;
    }

    private function getPaymentMethodNamesFromCollection(): array
    {
        $paymentMethods = $this->responseChecker->getCollection($this->client->index(Resources::PAYMENT_METHODS));

        return array_map(fn (array $paymentMethod) => $paymentMethod['translations']['en_US']['name'], $paymentMethods);
    }
}
