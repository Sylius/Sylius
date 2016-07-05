@paying_for_order
Feature: Changing the method after order confirmation
    In order to try different payment methods
    As a Customer
    I want to be able to change the method after order confirmation

    Background:
        Given the store operates on a single channel in "France"
        And default currency is USD
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying "PayPal Express Checkout"
        And the store allows paying "Offline"
        And I am logged in as "john@example.com"

    @ui
    Scenario: Retrying the payment with offline payment
        Given I added product "PHP T-Shirt" to the cart
        And I proceeded selecting "PayPal Express Checkout" payment method
        And I have confirmed my order
        And I tried to pay
        And I have cancelled my PayPal payment
        When I change payment method to "Offline"
        And I confirm my changes
        Then I should be redirected to the thank you page
