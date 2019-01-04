@paying_for_order
Feature: Having good number of items in changing payment method page
    In order to verify that I am changing the payment method of correct order
    As a Customer
    I want to see correct details about my order on changing the payment method page

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "john@example.com" identified by "password123"
        And the store allows paying "Offline"
        And the store allows paying "Cash on delivery"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And I am logged in as "john@example.com"

    @ui
    Scenario: Seeing correct quantity on payment retry page
        Given I have added 2 products "PHP T-Shirt" to the cart
        And I have proceeded selecting "Cash on delivery" payment method
        And I have confirmed order
        When I go to order details
        Then I should see 2 as number of items
