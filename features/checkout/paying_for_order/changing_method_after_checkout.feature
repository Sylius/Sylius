@paying_for_order
Feature: Changing the method after checkout
    In order to try different payment methods
    As a Customer
    I want to be able to change the method after checking out

    Background:
        Given the store operates on a single channel in "France"
        And default currency is USD
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store allows paying "PayPal Express Checkout"
        And the store allows paying "Offline"
        And I am logged in as "john@example.com"

    @todo
    Scenario: Retrying the payment with offline payment
        Given I added product "PHP T-Shirt" to the cart
        And I proceed selecting "PayPal Checkout Express" payment method
        And I have confirmed my order
        And I tried to pay
        And I canceled my PayPal payment
        And I am be able to pay again
        When I choose payment method "Offline"
        And try to pay again
        Then I should be redirected to the thank you page
