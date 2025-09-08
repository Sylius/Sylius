@checkout
Feature: Preventing placing an order with a disabled payment method when in checkout complete step
    In order to have my order shipped without issues
    As a Customer
    I want to be prevented from placing an order with a disabled shipping method

    Background:
        Given the store operates on a single channel in the "United States" named "US Web Store"
        And the store has a product "Ubi T-Shirt" priced at "$19.99"
        And the store has "Free" shipping method with "$4.00" fee
        And the store allows paying "Bank Transfer"
        And I am a logged in customer

    @ui @api
    Scenario: Being prevented from placing an order with a payment method that's has been disabled for Channel after completing the shipping method choice step - UI
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded through checkout process with "Free" shipping method
        And I have proceeded selecting "Bank Transfer" payment method
        But the store has disabled "Bank Transfer" payment method in Channel "US Web Store"
        When I try to confirm my order
        Then I should be informed that this payment method has been disabled
        And I should not see the thank you page
