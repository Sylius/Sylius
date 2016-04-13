@paying_for_order
Feature: Changing the method on the order payment page
    In order to try different payment methods
    As a Customer
    I want to be able to change the method after checking out

    Background:
        Given the store operates on a single channel in "France"
        And default currency is USD
        And there is user "john@example.com" identified by "password123"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store allows paying "PayPal Express Checkout"
        And it also allows offline payment
        And I am logged in as "john@example.com"

    @todo
    Scenario: Retrying the payment with offline payment
        Given I added product "PHP T-Shirt" to the cart
        And I confirmed my order selecting PayPal Checkout Express payment method
        But I failed to pay
        And I am redirected back to the order payment page
        When I try to pay again with offline method
        Then I should be redirected back to the thank you page
