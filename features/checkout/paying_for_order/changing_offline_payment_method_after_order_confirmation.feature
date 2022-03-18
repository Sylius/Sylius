@paying_for_order
Feature: Changing the offline payment method after order confirmation
    In order to try different payment methods
    As a Guest
    I want to be able to change the method after order confirmation

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying "Offline"
        And the store allows paying "Cash on delivery"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free

    @ui
    Scenario: Retrying the payment with different Offline payment
        Given I added product "PHP T-Shirt" to the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I have proceeded selecting "Free" shipping method
        And I have proceeded selecting "Cash on delivery" payment method
        And I have confirmed order
        And I retry the payment with "Offline" payment method
        Then I should have chosen "Offline" payment method

    @ui
    Scenario: Retrying the payment with different Offline payment works correctly together with inventory
        Given there is 1 unit of product "PHP T-Shirt" available in the inventory
        And this product is tracked by the inventory
        When I added product "PHP T-Shirt" to the cart
        And I complete addressing step with email "john@example.com" and "United States" based billing address
        And I have proceeded selecting "Free" shipping method
        And I have proceeded selecting "Cash on delivery" payment method
        And I have confirmed order
        And I retry the payment with "Offline" payment method
        Then I should have chosen "Offline" payment method

    @api
    Scenario: Changing chosen Offline payment method to another Offline payment method after checkout
        Given I added product "PHP T-Shirt" to the cart
        When I complete addressing step with email "john@example.com" and "United States" based billing address
        And I proceed selecting "Free" shipping method
        And I proceed selecting "Cash on delivery" payment method
        And I confirm my order
        And I change payment method to "Offline" after checkout
        Then I should have chosen "Offline" payment method
