@paying_for_order
Feature: Preventing placing an order with a payment method not available in channel
    In order to have my order shipped without issues
    As a Visitor
    I want to be prevented from placing an order with a payment method not available in my channel

    Background:
        Given the store operates on a single channel in the "United States" named "US Web Store"
        And the store has a product "Ubi T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying "Bank Transfer"
        And I am a logged in customer

    @api @ui
    Scenario: Being prevented from placing an order with a payment method that is not available in my channel
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded selecting "Bank Transfer" payment method
        And the payment method "Bank Transfer" has been disabled in "US Web Store" channel
        When I try to confirm my order
        Then I should be informed that this payment method has been disabled
        And I should not see the thank you page

    @api @ui
    Scenario: Being prevented from placing an order with a payment method that is disabled
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded selecting "Bank Transfer" payment method
        But the payment method "Bank Transfer" is disabled
        When I try to confirm my order
        Then I should be informed that this payment method has been disabled
        And I should not see the thank you page

    @api @ui
    Scenario: Being prevented from placing an order with a payment method that is disabled and not available in my channel
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded selecting "Bank Transfer" payment method
        But the payment method "Bank Transfer" is disabled
        And the payment method "Bank Transfer" has been disabled in "US Web Store" channel
        When I try to confirm my order
        Then I should be informed that this payment method has been disabled
        And I should not see the thank you page

    @api @ui
    Scenario: Being able to place an order with enabled payment method in my channel
        Given I added product "Ubi T-Shirt" to the cart
        And I have proceeded selecting "Bank Transfer" payment method
        When I try to confirm my order
        Then I should see the thank you page
