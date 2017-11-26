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

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class CheckoutThankYouContext implements Context
{
    /**
     * @var ThankYouPageInterface
     */
    private $thankYouPage;

    /**
     * @param ThankYouPageInterface $thankYouPage
     */
    public function __construct(ThankYouPageInterface $thankYouPage)
    {
        $this->thankYouPage = $thankYouPage;
    }

    /**
     * @When I go to order details
     */
    public function iGoToOrderDetails(): void
    {
        $this->thankYouPage->goToOrderDetails();
    }

    /**
     * @Then I should see the thank you page
     */
    public function iShouldSeeTheThankYouPage(): void
    {
        Assert::true($this->thankYouPage->hasThankYouMessage());
    }

    /**
     * @Then I should see the thank you page in :localeCode
     */
    public function iShouldSeeTheThankYouPageInLocale($localeCode): void
    {
        Assert::false($this->thankYouPage->isOpen(['_locale' => $localeCode]));
    }

    /**
     * @Then I should not see the thank you page
     */
    public function iShouldNotSeeTheThankYouPage(): void
    {
        Assert::false($this->thankYouPage->isOpen());
    }

    /**
     * @Then I should be informed with :paymentMethod payment method instructions
     */
    public function iShouldBeInformedWithPaymentMethodInstructions(PaymentMethodInterface $paymentMethod): void
    {
        Assert::same($this->thankYouPage->getInstructions(), $paymentMethod->getInstructions());
    }

    /**
     * @Then I should not see any instructions about payment method
     */
    public function iShouldNotSeeAnyInstructionsAboutPaymentMethod(): void
    {
        Assert::false($this->thankYouPage->hasInstructions());
    }

    /**
     * @Then I should not be able to change payment method
     */
    public function iShouldNotBeAbleToChangeMyPaymentMethod(): void
    {
        Assert::false($this->thankYouPage->hasChangePaymentMethodButton());
    }
}
