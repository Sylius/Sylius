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

namespace Sylius\Behat\Context\Ui\Shop;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use Sylius\Behat\NotificationType;
use Sylius\Behat\Page\Shop\Account\ChangePasswordPageInterface;
use Sylius\Behat\Page\Shop\Account\DashboardPageInterface;
use Sylius\Behat\Page\Shop\Account\LoginPageInterface;
use Sylius\Behat\Page\Shop\Account\Order\IndexPageInterface;
use Sylius\Behat\Page\Shop\Account\Order\ShowPageInterface;
use Sylius\Behat\Page\Shop\Account\ProfileUpdatePageInterface;
use Sylius\Behat\Service\NotificationCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Webmozart\Assert\Assert;

final class AccountContext implements Context
{
    public function __construct(
        private DashboardPageInterface $dashboardPage,
        private ProfileUpdatePageInterface $profileUpdatePage,
        private ChangePasswordPageInterface $changePasswordPage,
        private IndexPageInterface $orderIndexPage,
        private ShowPageInterface $orderShowPage,
        private LoginPageInterface $loginPage,
        private NotificationCheckerInterface $notificationChecker,
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @When I want to modify my profile
     */
    public function iWantToModifyMyProfile(): void
    {
        $this->profileUpdatePage->open();
    }

    /**
     * @When I specify the first name as :firstName
     * @When I remove the first name
     */
    public function iSpecifyTheFirstName($firstName = null): void
    {
        $this->profileUpdatePage->specifyFirstName($firstName);
    }

    /**
     * @When I specify the phone number as huge value
     */
    public function iSpecifyThePhoneNumberAsHugeValue(): void
    {
        $this->profileUpdatePage->specifyPhoneNumber(str_repeat('1', 256));
    }

    /**
     * @When I specify the last name as :lastName
     * @When I remove the last name
     */
    public function iSpecifyTheLastName($lastName = null): void
    {
        $this->profileUpdatePage->specifyLastName($lastName);
    }

    /**
     * @When I specify the customer email as :email
     * @When I remove the customer email
     */
    public function iSpecifyCustomerTheEmail($email = null): void
    {
        $this->profileUpdatePage->specifyEmail($email);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges(): void
    {
        $this->profileUpdatePage->saveChanges();
    }

    /**
     * @Then I should be notified that it has been successfully edited
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyEdited(): void
    {
        $this->notificationChecker->checkNotification('has been successfully updated.', NotificationType::success());
    }

    /**
     * @Then I should be notified that I can no longer change payment method of this order
     */
    public function iShouldBeNotifiedThatICanNoLongerChangePaymentMethodOfThisOrder(): void
    {
        Assert::true($this->orderIndexPage->hasFlashMessage('You can no longer change payment method of this order'));
    }

    /**
     * @Then my name should be :name
     * @Then my name should still be :name
     */
    public function myNameShouldBe($name): void
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerName($name));
    }

    /**
     * @Then my phone number should still be :phoneNumber
     */
    public function myPhoneNumberShouldBe(string $phoneNumber): void
    {
        $this->profileUpdatePage->open();

        Assert::same($this->profileUpdatePage->getPhoneNumber(), $phoneNumber, 'Phone number should be equal to %s, but is not.');
    }

    /**
     * @Then my email should be :email
     * @Then my email should still be :email
     */
    public function myEmailShouldBe($email): void
    {
        $this->dashboardPage->open();

        Assert::true($this->dashboardPage->hasCustomerEmail($email));
    }

    /**
     * @Then /^I should be notified that the (email|password|city|street|first name|last name) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element): void
    {
        Assert::true($this->profileUpdatePage->checkValidationMessageFor(
            StringInflector::nameToCode($element),
            sprintf('Please enter your %s.', $element),
        ));
    }

    /**
     * @Then I should be notified that the phone number is too long
     */
    public function iShouldBeNotifiedThatPhoneNumberIsTooLong(): void
    {
        Assert::true($this->profileUpdatePage->checkValidationMessageFor(
            'phone_number',
            'Phone number must not be longer than 255 characters.',
        ));
    }

    /**
     * @Then /^I should be notified that the (email) is invalid$/
     */
    public function iShouldBeNotifiedThatElementIsInvalid($element): void
    {
        Assert::true($this->profileUpdatePage->checkValidationMessageFor(
            StringInflector::nameToCode($element),
            sprintf('This %s is invalid.', $element),
        ));
    }

    /**
     * @Then I should be notified that the email is already used
     */
    public function iShouldBeNotifiedThatTheEmailIsAlreadyUsed(): void
    {
        Assert::true($this->profileUpdatePage->checkValidationMessageFor('email', 'This email is already used.'));
    }

    /**
     * @When /^I want to change my password$/
     */
    public function iWantToChangeMyPassword(): void
    {
        $this->changePasswordPage->open();
    }

    /**
     * @Given I change password from :oldPassword to :newPassword
     */
    public function iChangePasswordTo($oldPassword, $newPassword): void
    {
        $this->iSpecifyTheCurrentPasswordAs($oldPassword);
        $this->iSpecifyTheNewPasswordAs($newPassword);
        $this->iSpecifyTheConfirmationPasswordAs($newPassword);
    }

    /**
     * @Given I am changing this order's payment method
     */
    public function iWantToChangeThisOrdersPaymentMethod(): void
    {
        $this->iBrowseMyOrders();

        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderIndexPage->changePaymentMethod($order);
    }

    /**
     * @Then I should be notified that my password has been successfully changed
     */
    public function iShouldBeNotifiedThatMyPasswordHasBeenSuccessfullyChanged(): void
    {
        $this->notificationChecker->checkNotification('has been changed successfully!', NotificationType::success());
    }

    /**
     * @Given I specify the current password as :password
     */
    public function iSpecifyTheCurrentPasswordAs($password): void
    {
        $this->changePasswordPage->specifyCurrentPassword($password);
    }

    /**
     * @Given I specify the new password as :password
     */
    public function iSpecifyTheNewPasswordAs($password): void
    {
        $this->changePasswordPage->specifyNewPassword($password);
    }

    /**
     * @Given I confirm this password as :password
     */
    public function iSpecifyTheConfirmationPasswordAs($password): void
    {
        $this->changePasswordPage->specifyConfirmationPassword($password);
    }

    /**
     * @Then I should be notified that provided password is different than the current one
     */
    public function iShouldBeNotifiedThatProvidedPasswordIsDifferentThanTheCurrentOne(): void
    {
        Assert::true($this->changePasswordPage->checkValidationMessageFor(
            'current_password',
            'Provided password is different than the current one.',
        ));
    }

    /**
     * @Then I should be notified that the entered passwords do not match
     */
    public function iShouldBeNotifiedThatTheEnteredPasswordsDoNotMatch(): void
    {
        Assert::true($this->changePasswordPage->checkValidationMessageFor(
            'new_password',
            'The entered passwords don\'t match',
        ));
    }

    /**
     * @Then I should be notified that the password should be at least :length characters long
     */
    public function iShouldBeNotifiedThatThePasswordShouldBeAtLeastCharactersLong(int $length): void
    {
        Assert::true($this->changePasswordPage->checkValidationMessageFor(
            'new_password',
            sprintf('Password must be at least %s characters long.', $length),
        ));
    }

    /**
     * @Given I am browsing my orders
     * @When I browse my orders
     */
    public function iBrowseMyOrders(): void
    {
        $this->orderIndexPage->open();
    }

    /**
     * @When I change my payment method to :paymentMethod
     */
    public function iChangeMyPaymentMethodTo(PaymentMethodInterface $paymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderIndexPage->changePaymentMethod($order);
        $this->orderShowPage->choosePaymentMethod($paymentMethod);
        $this->orderShowPage->pay();
    }

    /**
     * @Then I try to change my payment method to :paymentMethod
     */
    public function iChoosePaymentMethod(PaymentMethodInterface $paymentMethod): void
    {
        $this->orderShowPage->choosePaymentMethod($paymentMethod);
        $this->orderShowPage->pay();
    }

    /**
     * @Then I should see a single order in the list
     */
    public function iShouldSeeASingleOrderInTheList(): void
    {
        Assert::same($this->orderIndexPage->countOrders(), 1);
    }

    /**
     * @Then this order should have :order number
     */
    public function thisOrderShouldHaveNumber(OrderInterface $order): void
    {
        Assert::true($this->orderIndexPage->isOrderWithNumberInTheList($order->getNumber()));
    }

    /**
     * @When I view the summary of the order :order
     * @When I view the summary of my order :order
     */
    public function iViewTheSummaryOfTheOrder(OrderInterface $order): void
    {
        $this->orderShowPage->open(['number' => $order->getNumber()]);
    }

    /**
     * @When I am viewing the summary of my last order
     */
    public function iViewingTheSummaryOfMyLastOrder(): void
    {
        $this->orderIndexPage->open();
        $this->orderIndexPage->openLastOrderPage();
    }

    /**
     * @When I log in as :email with :password password
     */
    public function iLogInAsWithPassword(string $email, string $password): void
    {
        $this->loginPage->open();
        $this->loginPage->specifyUsername($email);
        $this->loginPage->specifyPassword($password);
        $this->loginPage->logIn();
    }

    /**
     * @Then it should has number :orderNumber
     * @Then it should have the number :orderNumber
     */
    public function itShouldHasNumber(string $orderNumber): void
    {
        Assert::same($this->orderShowPage->getNumber(), $orderNumber);
    }

    /**
     * @Then I should see :customerName, :street, :postcode, :city, :countryName as shipping address
     */
    public function iShouldSeeAsShippingAddress($customerName, $street, $postcode, $city, $countryName): void
    {
        Assert::true($this->orderShowPage->hasShippingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then I should see :customerName, :street, :postcode, :city, :countryName as billing address
     */
    public function itShouldBeShippedTo($customerName, $street, $postcode, $city, $countryName): void
    {
        Assert::true($this->orderShowPage->hasBillingAddress($customerName, $street, $postcode, $city, $countryName));
    }

    /**
     * @Then I should see :total as order's total
     */
    public function iShouldSeeAsOrderSTotal($total): void
    {
        Assert::same($this->orderShowPage->getTotal(), $total);
    }

    /**
     * @Then I should see :itemsTotal as order's subtotal
     */
    public function iShouldSeeAsOrderSSubtotal($subtotal): void
    {
        Assert::same($this->orderShowPage->getSubtotal(), $subtotal);
    }

    /**
     * @Then I should see that I have to pay :paymentAmount for this order
     * @Then I should see :paymentTotal as payment total
     */
    public function iShouldSeeIHaveToPayForThisOrder($paymentAmount): void
    {
        Assert::same($this->orderShowPage->getPaymentPrice(), $paymentAmount);
    }

    /**
     * @Then I should see :numberOfItems items in the list
     */
    public function iShouldSeeItemsInTheList($numberOfItems): void
    {
        Assert::same($this->orderShowPage->countItems(), (int) $numberOfItems);
    }

    /**
     * @Then the product named :productName should be in the items list
     */
    public function theProductShouldBeInTheItemsList($productName): void
    {
        Assert::true($this->orderShowPage->isProductInTheList($productName));
    }

    /**
     * @Then I should have :paymentMethod payment method on my order
     */
    public function iShouldHavePaymentMethodOnMyOrder(PaymentMethodInterface $paymentMethod): void
    {
        /** @var OrderInterface $order */
        $order = $this->sharedStorage->get('order');

        $this->orderIndexPage->open();
        $this->orderIndexPage->changePaymentMethod($order);
        $this->orderShowPage->choosePaymentMethod($paymentMethod);

        Assert::same($this->orderShowPage->getChosenPaymentMethod(), $paymentMethod->getName());
    }

    /**
     * @Then I should see :itemPrice as item price
     */
    public function iShouldSeeAsItemPrice($itemPrice): void
    {
        Assert::same($this->orderShowPage->getItemPrice(), $itemPrice);
    }

    /**
     * @When I subscribe to the newsletter
     */
    public function iSubscribeToTheNewsletter(): void
    {
        $this->profileUpdatePage->subscribeToTheNewsletter();
    }

    /**
     * @Then I should be subscribed to the newsletter
     */
    public function iShouldBeSubscribedToTheNewsletter(): void
    {
        Assert::true($this->profileUpdatePage->isSubscribedToTheNewsletter());
    }

    /**
     * @Then I should see :provinceName as province in the shipping address
     */
    public function iShouldSeeAsProvinceInTheShippingAddress($provinceName): void
    {
        Assert::true($this->orderShowPage->hasShippingProvinceName($provinceName));
    }

    /**
     * @Then I should see :provinceName as province in the billing address
     */
    public function iShouldSeeAsProvinceInTheBillingAddress($provinceName): void
    {
        Assert::true($this->orderShowPage->hasBillingProvinceName($provinceName));
    }

    /**
     * @Then I should be redirected to my account dashboard
     */
    public function iShouldBeRedirectedToMyAccountDashboard(): void
    {
        Assert::true($this->dashboardPage->isOpen(), 'User should be on the account panel dashboard page but they are not.');
    }

    /**
     * @When I want to log in
     */
    public function iWantToLogIn(): void
    {
        $this->loginPage->tryToOpen();
    }

    /**
     * @Then I should see its payment status as :paymentStatus
     */
    public function shouldSeePaymentStatus(string $paymentStatus): void
    {
        Assert::same($this->orderShowPage->getPaymentStatus(), $paymentStatus);
    }

    /**
     * @Then I should see its order's payment status as :orderPaymentState
     */
    public function iShouldSeeItsOrderSPaymentStatusAs(string $orderPaymentState): void
    {
        Assert::same($this->orderShowPage->getOrderPaymentStatus(), $orderPaymentState);
    }

    /**
     * @Then the order's shipment status should be :orderShipmentStatus
     */
    public function theOrderShipmentStatusShouldBe(string $orderShipmentStatus): void
    {
        Assert::same($this->orderShowPage->getOrderShipmentStatus(), $orderShipmentStatus);
    }

    /**
     * @Then the shipment status should be :shipmentStatus
     */
    public function theShipmentStatusShouldBe(string $shipmentStatus): void
    {
        Assert::same($this->orderShowPage->getShipmentStatus(), $shipmentStatus);
    }

    /**
     * @Then I should be notified that the verification email has been sent
     */
    public function iShouldBeNotifiedThatTheVerificationEmailHasBeenSent(): void
    {
        $this->notificationChecker->checkNotification(
            'An email with the verification link has been sent to your email address.',
            NotificationType::success(),
        );
    }

    /**
     * @Then /^(?:my|his|her) account should not be verified$/
     */
    public function myAccountShouldNotBeVerified(): void
    {
        $this->dashboardPage->open();

        Assert::false($this->dashboardPage->isVerified());
    }

    /**
     * @Then I should not be logged in
     */
    public function iShouldNotBeLoggedIn(): void
    {
        try {
            $this->dashboardPage->open();
        } catch (UnexpectedPageException) {
            return;
        }

        throw new \InvalidArgumentException('Dashboard has been openned, but it shouldn\'t as customer should not be logged in');
    }

    /**
     * @Then I should not see my orders
     */
    public function iShouldNotSeeMyOrders(): void
    {
        Assert::false($this->orderIndexPage->isOpen());
    }

    /**
     * @Then I should be on the login page
     */
    public function iShouldBeOnTheLoginPage(): void
    {
        Assert::true($this->loginPage->isOpen());
    }
}
