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

namespace Sylius\Behat\Context\Api\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Webmozart\Assert\Assert;

final class ManagingPromotionsContext implements Context
{
    /** @var ApiClientInterface */
    private $apiAdminClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    public function __construct(
        ApiClientInterface $apiAdminClient,
        ResponseCheckerInterface $responseChecker
    ) {
        $this->apiAdminClient = $apiAdminClient;
        $this->responseChecker = $responseChecker;
    }

    /**
     * @Given I want to browse promotions
     * @When I browse promotions
     */
    public function iWantToBrowsePromotions(): void
    {
        $this->apiAdminClient->index();
    }

    /**
     * @Then I should see a single promotion in the list
     * @Then there should be :amount promotions
     */
    public function thereShouldBePromotion(int $amount = 1): void
    {
        Assert::same(
            count($this->responseChecker->getCollection($this->apiAdminClient->getLastResponse())),
            $amount
        );
    }

    /**
     * @Then the :promotionName promotion should exist in the registry
     */
    public function thePromotionShouldAppearInTheRegistry(string $promotionName): void
    {
        $promotion = $this->getPromotionByPromotionNameFromResponseCollection(
            $promotionName,
            $this->responseChecker->getCollection($this->apiAdminClient->getLastResponse())
        );

        Assert::notNull($promotion);
    }

    /**
     * @Then /^(this promotion) should be coupon based$/
     */
    public function thisPromotionShouldBeCouponBased(PromotionInterface $promotion): void
    {
        $returnedPromotion = $this->getPromotionByPromotionNameFromResponseCollection(
            $promotion->getName(),
            $this->responseChecker->getCollection($this->apiAdminClient->getLastResponse())
        );

        Assert::true($returnedPromotion['couponBased']);
    }

    /**
     * @Then /^I should be able to manage coupons for (this promotion)$/
     */
    public function iShouldBeAbleToManageCouponsForThisPromotion(PromotionInterface $promotion)
    {
        $returnedPromotion = $this->getPromotionByPromotionNameFromResponseCollection(
            $promotion->getName(),
            $this->responseChecker->getCollection($this->apiAdminClient->getLastResponse())
        );

        Assert::keyExists($returnedPromotion, 'coupons');
    }

    private function getPromotionByPromotionNameFromResponseCollection(
        string $promotionName,
        array $responseCollection
    ): ?array {
        foreach ($responseCollection as $promotion) {
            if ($promotion['name'] === $promotionName) {
                return $promotion;
            }
        }

        return null;
    }
}
