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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Behat\Mink\Exception\ElementNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Checkout\CompletePageInterface;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Webmozart\Assert\Assert;

final readonly class CheckoutCompleteContext implements Context
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private CompletePageInterface $completePage,
        private NotificationCheckerInterface $notificationChecker,
        private ThankYouPageInterface $thankYouPage,
        private OrderRepositoryInterface $orderRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @When I try to open checkout complete page
     * @When I want to complete checkout
     */
    public function iTryToOpenCheckoutCompletePage(): void
    {
        $this->completePage->tryToOpen();
    }

    /**
     * @When I decide to change the payment method
     */
    public function iGoToThePaymentStep(): void
    {
        $this->completePage->changePaymentMethod();
    }

    /**
     * @When /^I provide additional note like "([^"]+)"$/
     */
    public function iProvideAdditionalNotesLike($notes): void
    {
        $this->sharedStorage->set('additional_note', $notes);
        $this->completePage->addNotes($notes);
    }

    /**
     * @When I return to the checkout summary step
     */
    public function iReturnToTheCheckoutSummaryStep(): void
    {
        $this->completePage->open();
    }

    /**
     * @Given I have confirmed order
     * @Given the visitor confirm his order
     * @Given the customer confirm his order
     * @Given the customer confirmed the order
     * @When I confirm my order
     * @When I try to confirm my order
     */
    public function iConfirmMyOrder(): void
    {
        if (!$this->completePage->isOpen()) {
            $this->completePage->open();
        }

        $this->completePage->confirmOrder();

        $order = $this->orderRepository->findLatest(1)[0] ?? null;

        if ($order === null) {
            return;
        }

        $this->entityManager->refresh($order);

        $this->sharedStorage->set('order', $order);
        $this->sharedStorage->set('order_number', $order->getNumber());
    }

    /**
     * @Then I should be on the checkout complete step
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutCompleteStep(): void
    {
        $this->completePage->verify();
    }

    /**
     * @Then my order's shipping address should be to :fullName
     */
    public function iShouldSeeThisShippingAddressAsShippingAddress(string $fullName): void
    {
        $address = $this->sharedStorage->get('shipping_address_' . StringInflector::nameToLowercaseCode($fullName));

        Assert::true($this->completePage->hasShippingAddress($address));
    }

    /**
     * @Then my order's billing address should be to :fullName
     */
    public function iShouldSeeThisBillingAddressAsBillingAddress(string $fullName): void
    {
        $address = $this->sharedStorage->get('billing_address_' . StringInflector::nameToLowercaseCode($fullName));

        Assert::true($this->completePage->hasBillingAddress($address));
    }

    /**
     * @Then address to :fullName should be used for both shipping and billing of my order
     */
    public function iShouldSeeThisShippingAddressAsShippingAndBillingAddress(string $fullName): void
    {
        $this->iShouldSeeThisShippingAddressAsShippingAddress($fullName);
        $this->iShouldSeeThisBillingAddressAsBillingAddress($fullName);
    }

    /**
     * @Then I should have :quantity :productName products in the cart
     */
    public function iShouldHaveProductsInTheCart(string $quantity, string $productName): void
    {
        Assert::true($this->completePage->hasItemWithProductAndQuantity($productName, $quantity));
    }

    /**
     * @Then my order shipping should be :price
     */
    public function myOrderShippingShouldBe(string $price): void
    {
        Assert::contains($this->completePage->getShippingTotal(), $price);
    }

    /**
     * @Then I should not see shipping total
     */
    public function iShouldNotSeeShippingTotal(): void
    {
        Assert::false($this->completePage->hasShippingTotal());
    }

    /**
     * @Then /^the ("[^"]+" product) should have unit price discounted by ("[^"]+")$/
     */
    public function theShouldHaveUnitPriceDiscountedFor(ProductInterface $product, int $amount): void
    {
        Assert::true($this->completePage->hasProductDiscountedUnitPriceBy($product, $amount));
    }

    /**
     * @Then /^my order total should be ("(?:\£|\$)\d+(?:\.\d+)?")$/
     */
    public function myOrderTotalShouldBe(int $total): void
    {
        Assert::true($this->completePage->hasOrderTotal($total));
    }

    /**
     * @Then my order promotion total should be :promotionTotal
     */
    public function myOrderPromotionTotalShouldBe(string $promotionTotal): void
    {
        Assert::true($this->completePage->hasPromotionTotal($promotionTotal));
    }

    /**
     * @Then :promotionName should be applied to my order
     */
    public function shouldBeAppliedToMyOrder(string $promotionName): void
    {
        Assert::true($this->completePage->hasOrderPromotion($promotionName));
    }

    /**
     * @Then :promotionName should be applied to my order shipping
     */
    public function shouldBeAppliedToMyOrderShipping(string $promotionName): void
    {
        Assert::true($this->completePage->hasShippingPromotion($promotionName));
    }

    /**
     * @Given my tax total should be :taxTotal
     */
    public function myTaxTotalShouldBe(string $taxTotal): void
    {
        Assert::same($this->completePage->getTaxTotal(), $taxTotal);
    }

    /**
     * @Then my order's shipping method should be :shippingMethod
     */
    public function myOrdersShippingMethodShouldBe(ShippingMethodInterface $shippingMethod): void
    {
        Assert::true($this->completePage->hasShippingMethod($shippingMethod));
    }

    /**
     * @Then my order's payment method should be :paymentMethod
     */
    public function myOrdersPaymentMethodShouldBe(PaymentMethodInterface $paymentMethod): void
    {
        Assert::same($this->completePage->getPaymentMethodName(), $paymentMethod->getName());
    }

    /**
     * @Then the :product product should have unit price :price
     */
    public function theProductShouldHaveUnitPrice(ProductInterface $product, string $price): void
    {
        Assert::true($this->completePage->hasProductUnitPrice($product, $price));
    }

    /**
     * @Then /^I should be notified that (this product) does not have sufficient stock$/
     * @Then I should be notified that product :product does not have sufficient stock
     */
    public function iShouldBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::true($this->completePage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then /^I should not be notified that (this product) does not have sufficient stock$/
     */
    public function iShouldNotBeNotifiedThatThisProductDoesNotHaveSufficientStock(ProductInterface $product): void
    {
        Assert::false($this->completePage->hasProductOutOfStockValidationMessage($product));
    }

    /**
     * @Then my order's locale should be :locale
     */
    public function myOrderLocaleShouldBe(LocaleInterface $locale): void
    {
        Assert::true($this->completePage->hasLocale($locale->getName($locale->getCode())));
    }

    /**
     * @Then I should see :provinceName in the shipping address
     */
    public function iShouldSeeInTheShippingAddress(string $provinceName): void
    {
        Assert::true($this->completePage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName in the billing address
     */
    public function iShouldSeeInTheBillingAddress(string $provinceName): void
    {
        Assert::true($this->completePage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then I should not see any information about payment method
     */
    public function iShouldNotSeeAnyInformationAboutPaymentMethod(): void
    {
        Assert::false($this->completePage->hasPaymentMethod());
    }

    /**
     * @Then I should not be able to confirm order because products do not fit :shippingMethod requirements
     */
    public function iShouldNotBeAbleToConfirmOrderBecauseDoNotBelongsToShippingCategory(ShippingMethodInterface $shippingMethod): void
    {
        $this->completePage->confirmOrder();

        Assert::same(
            $this->completePage->getValidationErrors(),
            sprintf(
                'Product does not fit requirements for %s shipping method. Please reselect your shipping method.',
                $shippingMethod->getName(),
            ),
        );
    }

    /**
     * @Then /^I should be informed that (this promotion) is no longer applied$/
     */
    public function iShouldBeInformedThatMyPromotionIsNoLongerApplied(PromotionInterface $promotion): void
    {
        $this->notificationChecker->checkNotification(
            sprintf('You are no longer eligible for this promotion %s.', $promotion->getName()),
            NotificationType::failure(),
        );
    }

    /**
     * @Then /^I should be informed that (this payment method) has been disabled$/
     */
    public function iShouldBeInformedThatThisPaymentMethodHasBeenDisabled(PaymentMethodInterface $paymentMethod): void
    {
        Assert::same(
            $this->completePage->getValidationErrors(),
            sprintf(
                'This payment method %s has been disabled. Please reselect your payment method.',
                $paymentMethod->getName(),
            ),
        );
    }

    /**
     * @Then /^I should be informed that (this product) has been disabled$/
     */
    public function iShouldBeInformedThatThisProductHasBeenDisabled(ProductInterface $product): void
    {
        Assert::same(
            $this->completePage->getValidationErrors(),
            sprintf(
                'This product %s has been disabled.',
                $product->getName(),
            ),
        );
    }

    /**
     * @Then my order should not be placed due to changed order total
     */
    public function myOrderShouldNotBePlacedDueToChangedOrderTotal(): void
    {
        $this->notificationChecker->checkNotification(
            'Your order total has been changed, check your order information and confirm it again.',
            NotificationType::failure(),
        );
        Assert::false($this->thankYouPage->isOpen());
    }

    /**
     * @Then /^(this promotion) should give "([^"]+)" discount on shipping$/
     */
    public function thisPromotionShouldGiveDiscountOnShipping(PromotionInterface $promotion, string $discount): void
    {
        Assert::true($this->completePage->hasShippingPromotionWithDiscount($promotion->getName(), $discount));
    }

    /**
     * @Then /^I should be informed that (this variant) has been disabled$/
     */
    public function iShouldBeInformedThatThisVariantHasBeenDisabled(ProductVariantInterface $productVariant): void
    {
        Assert::same(
            $this->completePage->getValidationErrors(),
            sprintf(
                'This product %s has been disabled.',
                $productVariant->getName(),
            ),
        );
    }

    /**
     * @Then I should not be able to proceed checkout complete step
     */
    public function iShouldNotBeAbleToProceedCheckoutCompleteStep(): void
    {
        $this->completePage->tryToOpen();

        try {
            $this->completePage->confirmOrder();
        } catch (ElementNotFoundException) {
            return;
        }

        throw new UnexpectedPageException('It should not be possible to complete checkout complete step.');
    }

    /**
     * @Then I should not be able to confirm order because the :shippingMethodName shipping method is not available
     */
    public function iShouldNotBeAbleToConfirmOrderBecauseTheShippingMethodIsNotAvailable(string $shippingMethodName): void
    {
        Assert::same(
            $this->completePage->getValidationErrors(),
            sprintf(
                'The "%s" shipping method is not available. Please reselect your shipping method.',
                $shippingMethodName,
            ),
        );
    }

    /**
     * @When /^I should see (product "[^"]+") with unit price ("[^"]+")$/
     */
    public function iShouldSeeWithUnitPrice(ProductInterface $product, int $unitPrice): void
    {
        Assert::same($this->completePage->getProductUnitPrice($product), $unitPrice);
    }
}
