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
use Sylius\Behat\Page\Shop\Account\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Order\ShowPageInterface as OrderDetailsPage;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Webmozart\Assert\Assert;

final class CheckoutThankYouContext implements Context
{
    public function __construct(
        private ThankYouPageInterface $thankYouPage,
        private ShowPageInterface $orderShowPage,
        private OrderRepositoryInterface $orderRepository,
        private OrderDetailsPage $orderDetails,
    ) {
    }

    /**
     * @When I go to the change payment method page
     */
    public function iGoToTheChangePaymentMethodPage(): void
    {
        $this->thankYouPage->goToTheChangePaymentMethodPage();
    }

    /**
     * @When I proceed to the registration
     */
    public function iProceedToTheRegistration(): void
    {
        $this->thankYouPage->createAccount();
    }

    /**
     * @Then I should be able to access this order's details
     */
    public function iShouldBeAbleToAccessThisOrderDetails(): void
    {
        $this->thankYouPage->goToOrderDetailsInAccount();

        $number = $this->orderShowPage->getNumber();

        Assert::same($this->orderRepository->findLatest(1)[0]->getNumber(), $number);
    }

    /**
     * @Then I should see the thank you page
     * @Then the visitor should see the thank you page
     * @Then the customer should see the thank you page
     */
    public function iShouldSeeTheThankYouPage()
    {
        Assert::true($this->thankYouPage->hasThankYouMessage());
    }

    /**
     * @Then I should see the thank you page in :localeCode
     */
    public function iShouldSeeTheThankYouPageInLocale($localeCode)
    {
        Assert::false($this->thankYouPage->isOpen(['_locale' => $localeCode]));
    }

    /**
     * @Then I should not see the thank you page
     */
    public function iShouldNotSeeTheThankYouPage()
    {
        Assert::false($this->thankYouPage->isOpen());
    }

    /**
     * @Then I should be informed with :paymentMethod payment method instructions
     */
    public function iShouldBeInformedWithPaymentMethodInstructions(PaymentMethodInterface $paymentMethod)
    {
        Assert::same($this->thankYouPage->getInstructions(), $paymentMethod->getInstructions());
    }

    /**
     * @Then I should not see any instructions about payment method
     */
    public function iShouldNotSeeAnyInstructionsAboutPaymentMethod()
    {
        Assert::false($this->thankYouPage->hasInstructions());
    }

    /**
     * @Then I should not be able to change payment method
     */
    public function iShouldNotBeAbleToChangeMyPaymentMethod()
    {
        Assert::false($this->thankYouPage->hasChangePaymentMethodButton());
    }

    /**
     * @Then I should be able to proceed to the registration
     */
    public function iShouldBeAbleToProceedToTheRegistration(): void
    {
        Assert::true($this->thankYouPage->hasRegistrationButton());
    }

    /**
     * @Then I should not be able to proceed to the registration
     */
    public function iShouldNotBeAbleToProceedToTheRegistration(): void
    {
        Assert::false($this->thankYouPage->hasRegistrationButton());
    }
}
