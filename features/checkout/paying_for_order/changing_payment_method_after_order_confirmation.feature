@paying_for_order
Feature: Changing the method after order confirmation
    In order to try different payment methods
    As a Customer
    I want to be able to change the method after order confirmation

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store allows paying "Offline"
        And the store allows paying "Cash on delivery"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui
    Scenario: Retrying the payment with offline payment
        Given I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Cash on delivery" payment method
        And I have confirmed order
        When I go to order details
        And I try to pay with "Offline" payment method
        Then I should see the thank you page

    @ui
    Scenario: Retrying the payment with offline payment works correctly together with inventory
        Given there is 1 unit of product "PHP T-Shirt" available in the inventory
        And this product is tracked by the inventory
        And I added product "PHP T-Shirt" to the cart
        And I have proceeded selecting "Cash on delivery" payment method
        And I have confirmed order
        When I go to order details
        And I try to pay with "Offline" payment method
        Then I should see the thank you page
