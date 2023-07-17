@checkout
Feature: Preventing placing an order with a disabled shipping method
    In order to ship my order properly
    As a Customer
    I want to not be able to place an order with a disabled shipping method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Ubi T-Shirt" priced at "$19.99"
        And I am a logged in customer

    @ui @api
    Scenario: Prevent placing an order with a shipping method disabled after completing the shipping method choice step
        Given I added product "Arizona Green Tea" to the cart
        And I have completed addressing step with email "eivor@assassins.com" and "United States" based billing address
        And I have proceeded order with "Raven Post" shipping method and "Offline" payment
        And I want to complete checkout
        But this shipping method has been disabled
        When I try to confirm my order
        Then I should not be able to confirm order because the "Raven Post" shipping method is not available
