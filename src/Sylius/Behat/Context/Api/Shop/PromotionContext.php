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

namespace Sylius\Behat\Context\Api\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Webmozart\Assert\Assert;

final class PromotionContext implements Context
{
    public function __construct(
        private ApiClientInterface $ordersClient,
        private SharedStorageInterface $sharedStorage,
        private ResponseCheckerInterface $responseChecker
    ) {
    }

    /**
     * @When I use coupon with code :couponCode
     * @When I remove coupon from my cart
     */
    public function iUseCouponWithCode(string $couponCode = null): void
    {
        $this->useCouponCode($couponCode);
    }

    /**
     * @Then I should be notified that the coupon is invalid
     */
    public function iShouldBeNotifiedThatCouponIsInvalid(): void
    {
        $response = $this->ordersClient->getLastResponse();

        Assert::same($response->getStatusCode(), 422);
        Assert::same($this->responseChecker->getError($response), 'couponCode: Coupon code is invalid.');
    }

    private function getCartTokenValue(): ?string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        return null;
    }

    private function useCouponCode(?string $couponCode): void
    {
        $this->ordersClient->buildUpdateRequest($this->getCartTokenValue());
        $this->ordersClient->setRequestData(['couponCode' => $couponCode]);
        $this->ordersClient->update();
    }
}
