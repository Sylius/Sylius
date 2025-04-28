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

namespace Sylius\Behat\Context\Api\Shop\Checkout;

use ApiPlatform\Metadata\IriConverterInterface;
use Behat\Behat\Context\Context;
use Behat\Step\Then;
use Behat\Step\When;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\Repository\ShippingMethodRepositoryInterface;
use Webmozart\Assert\Assert;

final readonly class CheckoutShippingContext implements Context
{
    /** @param ShippingMethodRepositoryInterface<ShippingMethodInterface> $shippingMethodRepository */
    public function __construct(
        private ApiClientInterface $client,
        private ResponseCheckerInterface $responseChecker,
        private SharedStorageInterface $sharedStorage,
        private IriConverterInterface $iriConverter,
        private ShippingMethodRepositoryInterface $shippingMethodRepository,
    ) {
    }

    #[When('I want to complete the shipping step')]
    #[When('I go to the shipping step')]
    #[When('the customer wants to complete the shipping step')]
    public function iWantToCompleteTheShippingStep(): void
    {
        // Intentionally left blank, as this is a UI-specific action.
    }

    #[When('I try to select non-existing shipping method')]
    public function iTryToSelectNonExistingShippingMethod(): void
    {
        $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));
        $content = $this->responseChecker->getResponseContent($response);

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => '/api/v2/shop/shipping-methods/NON_EXISTING'],
        );
    }

    #[When('I complete the shipping step with the first shipping method')]
    public function iCompleteTheShippingStepWithTheFirstShippingMethod(): void
    {
        /** @var ShippingMethodInterface $shippingMethod */
        $shippingMethod = $this->shippingMethodRepository->findOneBy([]);

        $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        $content = $this->responseChecker->getResponseContent($this->client->getLastResponse());

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => $this->iriConverter->getIriFromResource($shippingMethod)],
        );
    }

    #[When('I change shipping method to :shippingMethod')]
    #[When('I proceed with :shippingMethod shipping method')]
    #[When('I proceed with selecting :shippingMethod shipping method')]
    #[When('I select :shippingMethod shipping method')]
    #[When('I try to change shipping method to :shippingMethod')]
    #[When('I try to select :shippingMethod shipping method')]
    #[When('the customer has proceeded with :shippingMethod shipping method')]
    #[When('the customer proceeds with :shippingMethod shipping method')]
    #[When('the visitor has proceeded with :shippingMethod shipping method')]
    #[When('the visitor proceeds with :shippingMethod shipping method')]
    public function iTryToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->chooseShippingMethod($shippingMethod);
    }

    #[Then('the checkout shipping method step should be completed')]
    public function theCheckoutShippingMethodStepShouldBeCompleted(): void
    {
        Assert::same($this->getCheckoutState(), OrderCheckoutStates::STATE_SHIPPING_SELECTED);
    }

    #[Then('I should see that there is no assigned shipping method')]
    public function iShouldSeeThatThereIsNoAssignedShippingMethod(): void
    {
        $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        Assert::isEmpty($this->responseChecker->getValue($response, 'shipments'));
    }

    #[Then('there should not be any shipping method available to choose')]
    public function thereShouldNotBeAnyShippingMethodAvailableToChoose(): void
    {
        $response = $this->client->requestGet('shipping-methods');

        Assert::isEmpty($this->responseChecker->getCollection($response));
    }

    #[Then('I should not be able to select :shippingMethod shipping method')]
    public function iShouldNotBeAbleToSelectShippingMethod(ShippingMethodInterface $shippingMethod): void
    {
        $this->chooseShippingMethod($shippingMethod);

        Assert::same($this->client->getLastResponse()->getStatusCode(), 422);
        Assert::true($this->responseChecker->isViolationWithMessageInResponse($this->client->getLastResponse(), sprintf(
            'The shipping method %s is not available for this order. Please reselect your shipping method.',
            $shippingMethod->getName(),
        )));
    }

    #[Then('I should see that this shipping method is not available for this address')]
    #[Then('I should see that this shipping method is also not available for this address')]
    public function iShouldSeeThatThisShippingMethodIsNotAvailableForThisAddress(): void
    {
        Assert::true(
            $this->responseChecker->hasViolationWithMessage(
                $this->client->getLastResponse(),
                sprintf(
                    'The shipping method %s is not available for this order. Please reselect your shipping method.',
                    $this->sharedStorage->get('shipping_method'),
                ),
            ),
            sprintf(
                'Expected to see message that shipping method "%s" is not available. Got message: "%s".',
                $this->sharedStorage->get('shipping_method'),
                $this->responseChecker->getError($this->client->getLastResponse()),
            ),
        );
    }

    #[Then('I should be informed that shipping method with code :code does not exist')]
    public function iShouldBeInformedThatShippingMethodWithCodeDoesNotExist(string $code): void
    {
        Assert::true($this->responseChecker->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            sprintf('The shipping method with %s code does not exist.', $code),
        ));
    }

    public function chooseShippingMethod(?ShippingMethodInterface $shippingMethod = null): void
    {
        $response = $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        // Lack of authorization
        if (!$this->responseChecker->isShowSuccessful($response)) {
            return;
        }

        $content = $this->responseChecker->getResponseContent($response);

        $this->client->requestPatch(
            uri: sprintf(
                'orders/%s/shipments/%s',
                $this->sharedStorage->get('cart_token'),
                $content['shipments'][0]['id'],
            ),
            body: ['shippingMethod' => $this->iriConverter->getIriFromResource(
                $shippingMethod ?? $this->shippingMethodRepository->findOneBy([]),
            )],
        );
    }

    private function getCheckoutState(): string
    {
        $this->client->requestGet(sprintf('orders/%s', $this->sharedStorage->get('cart_token')));

        $response = $this->client->getLastResponse();

        return $this->responseChecker->getValue($response, 'checkoutState');
    }
}
