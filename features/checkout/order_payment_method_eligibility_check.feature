@heckout
Feature: Order payment method eligibility check
    In order to have valid payment method in my order
    As a Customer
    I want to have enabled payment method in my order

    Background:
        Given the store operates on a single channel in the "United States" named "US Web Store"
        And the store has a product "Ubi T-Shirt" priced at "$19.99"
        And the store has "Raven Post" shipping method with "$4.00" fee
        And the store allows paying "Offline"
        And I am a logged in customer

    @ui @api
    Scenario: Being prevented from placing an order with a shipping method that's disabled after completing the shipping method choice step
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded through checkout process with "Raven Post" shipping method
        But this shipping method has been disabled
        When I try to confirm my order
        Then I should not be able to confirm order because the "Raven Post" shipping method is not available
