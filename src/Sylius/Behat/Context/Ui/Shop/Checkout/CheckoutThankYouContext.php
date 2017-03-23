<?php

namespace Sylius\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Shop\Order\ThankYouPageInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
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
    public function iGoToOrderDetails()
    {
        $this->thankYouPage->goToOrderDetails();
    }

    /**
     * @Then I should see the thank you page
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
}
