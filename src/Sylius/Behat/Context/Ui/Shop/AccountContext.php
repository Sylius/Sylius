<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\PageInterface;
use Sylius\Behat\Page\Shop\Account\ChangePasswordPageInterface;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\Order\IndexPageInterface;
use Sylius\Behat\Page\Shop\Account\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class AccountContext implements Context
{
    /**
     * @var DashboardPageInterface
     */
    private $dashboardPage;

    /**
     * @var ProfileUpdatePageInterface
     */
    private $profileUpdatePage;

    /**
     * @var ChangePasswordPageInterface
     */
    private $changePasswordPage;

    /**
     * @var IndexPageInterface
     */
    private $orderIndexPage;

    /**
     * @var ShowPageInterface
     */
    private $orderShowPage;

    /**
     * @var NotificationCheckerInterface
     */
    private $notificationChecker;

    /**
     * @param DashboardPageInterface $dashboardPage
     * @param ProfileUpdatePageInterface $profileUpdatePage
     * @param ChangePasswordPageInterface $changePasswordPage
     * @param IndexPageInterface $orderIndexPage
     * @param ShowPageInterface $orderShowPage
     * @param NotificationCheckerInterface $notificationChecker
     */
    public function __construct(
        DashboardPageInterface $dashboardPage,
        ProfileUpdatePageInterface $profileUpdatePage,
        ChangePasswordPageInterface $changePasswordPage,
        IndexPageInterface $orderIndexPage,
        ShowPageInterface $orderShowPage,
        NotificationCheckerInterface $notificationChecker
    ) {
        $this->dashboardPage = $dashboardPage;
        $this->profileUpdatePage = $profileUpdatePage;
        $this->changePasswordPage = $changePasswordPage;
        $this->orderIndexPage = $orderIndexPage;
        $this->orderShowPage = $orderShowPage;
        $this->notificationChecker = $notificationChecker;
    }

    /**
     * @When I want to modify my profile
     */
    public function iWantToModifyMyProfile()
    {
        $this->profileUpdatePage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName($firstName = null)
    {
        $this->profileUpdatePage->specifyFirstName($firstName);
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName($lastName = null)
    {
        $this->profileUpdatePage->specifyLastName($lastName);
    }

    /**
     * @When I specify the customer email as :email
     * @When I remove the customer email
     */
    public function iSpecifyCustomerTheEmail($email = null)
    {
        $this->profileUpdatePage->specifyEmail($email);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->profileUpdatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited()
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then my name should be :name
     * @Then my name should still be :name
     */
    public function myNameShouldBe($name)
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerName($name));
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe($email)
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerEmail($email));
    }

    /**
     * @Then /^I should be notified that the (email|password|city|street|first name|last name) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        $this->assertFieldValidationMessage(
            $this->profileUpdatePage,
            StringInflector::nameToCode($element),
            sprintf('Please enter your %s.', $element)
        );
    }

    /**
     * @Then /^I should be notified that the (email) is invalid$/
     */
    public function iShouldBeNotifiedThatElementIsInvalid($element)
    {
        $this->assertFieldValidationMessage(
            $this->profileUpdatePage,
            StringInflector::nameToCode($element),
            sprintf('This %s is invalid.', $element)
        );
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed()
    {
        $this->assertFieldValidationMessage($this->profileUpdatePage, 'email', 'This email is already used.');
    }

    /**
     * @Given /^I want to change my password$/
     */
    public function iWantToChangeMyPassword()
    {
        $this->changePasswordPage->open();
    }

    /**
     * @Given I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo($oldPassword, $newPassword)
    {
        $this->iSpecifyTheCurrentPasswordAs($oldPassword);
        $this->iSpecifyTheNewPasswordAs($newPassword);
        $this->iSpecifyTheConfirmationPasswordAs($newPassword);
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyChanged()
    {
        $this->notificationChecker->checkNotification('has been changed successfully!', NotificationType::success());
    }

    /**
     * @Given I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs($password)
    {
        $this->changePasswordPage->specifyCurrentPassword($password);
    }

    /**
     * @Given I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs($password)
    {
        $this->changePasswordPage->specifyNewPassword($password);
    }

    /**
     * @Given I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs($password)
    {
        $this->changePasswordPage->specifyConfirmationPassword($password);
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne()
    {
        $this->assertFieldValidationMessage(
            $this->changePasswordPage,
            'current_password',
            'Provided password is different than the current one.'
        );
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch()
    {
        $this->assertFieldValidationMessage(
            $this->changePasswordPage,
            'new_password',
            'The entered passwords don\'t match'
        );
    }

    /**
     * @Then I should be notified that the password should be at least 4 characters long
     */
    public function iShouldBeNotifiedThatThePasswordShouldBeAtLeastCharactersLong()
    {
        $this->assertFieldValidationMessage(
            $this->changePasswordPage,
            'new_password',
            'Password must be at least 4 characters long.'
        );
    }

    /**
     * @When I browse my orders
     */
    public function iBrowseMyOrders()
    {
        $this->orderIndexPage->open();
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList()
    {
        Assert::same($this->orderIndexPage->countOrders(), 1);
    }

    /**
     * @Then this order should have :order number
     */
    public function thisOrderShouldHaveNumber(OrderInterface $order)
    {
        Assert::true($this->orderIndexPage->isOrderWithNumberInTheList($order->getNumber()));
    }

    /**
     * @When I view the summary of the order :order
     */
    public function iViewTheSummaryOfTheOrder(OrderInterface $order)
    {
        $this->orderShowPage->open(['number' => $order->getNumber()]);
    }

    /**
     * @When I am viewing the summary of my last order
     */
    public function iViewingTheSummaryOfMyLastOrder()
    {
        $this->orderIndexPage->open();
        $this->orderIndexPage->openLastOrderPage();
    }

    /**
     * @Then it should has number :orderNumber
     */
    public function itShouldHasNumber($orderNumber)
    {
        Assert::same($this->orderShowPage->getNumber(), $orderNumber);
    }

    /**
     * @Then I should see :customerName, :street, :postcode, :city, :countryName as shipping address
     */
    public function iShouldSeeAsShippingAddress($customerName, $street, $postcode, $city, $countryName)
    {
        Assert::true($this->orderShowPage->hasShippingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then I should see :customerName, :street, :postcode, :city, :countryName as billing address
     */
    public function itShouldBeShippedTo($customerName, $street, $postcode, $city, $countryName)
    {
        Assert::true($this->orderShowPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then I should see :total as order's total
     */
    public function iShouldSeeAsOrderSTotal($total)
    {
        Assert::same($this->orderShowPage->getTotal(), $total);
    }

    /**
     * @Then I should see :itemsTotal as order's subtotal
     */
    public function iShouldSeeAsOrderSSubtotal($subtotal)
    {
        Assert::same($this->orderShowPage->getSubtotal(), $subtotal);
    }

    /**
     * @Then I should see that I have to pay :paymentAmount for this order
     * @Then I should see :paymentTotal as payment total
     */
    public function iShouldSeeIHaveToPayForThisOrder($paymentAmount)
    {
        Assert::same($this->orderShowPage->getPaymentPrice(), $paymentAmount);
    }

    /**
     * @Then I should see :numberOfItems items in the list
     */
    public function iShouldSeeItemsInTheList($numberOfItems)
    {
        Assert::same($this->orderShowPage->countItems(), (int) $numberOfItems);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList($productName)
    {
        Assert::true($this->orderShowPage->isProductInTheList($productName));
    }

    /**
     * @Then I should see :itemPrice as item price
     */
    public function iShouldSeeAsItemPrice($itemPrice)
    {
        Assert::same($this->orderShowPage->getItemPrice(), $itemPrice);
    }

    /**
     * @When I subscribe to the newsletter
     */
    public function iSubscribeToTheNewsletter()
    {
        $this->profileUpdatePage->subscribeToTheNewsletter();
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter()
    {
        Assert::true($this->profileUpdatePage->isSubscribedToTheNewsletter());
    }

    /**
     * @Then I should see :provinceName as province in the shipping address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress($provinceName)
    {
        Assert::true($this->orderShowPage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName as province in the billing address
     */
    public function iShouldSeeAsProvinceInTheBillingAddress($provinceName)
    {
        Assert::true($this->orderShowPage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then /^I should be able to change payment method for (this order)$/
     */
    public function iShouldBeAbleToChangePaymentMethodForThisOrder(OrderInterface $order)
    {
        Assert::true($this->orderIndexPage->isItPossibleToChangePaymentMethodForOrder($order));
    }

    /**
     * @param PageInterface $page
     * @param string $element
     * @param string $expectedMessage
     */
    private function assertFieldValidationMessage(PageInterface $page, $element, $expectedMessage)
    {
        Assert::true($page->checkValidationMessageFor($element, $expectedMessage));
    }
}
