@paying_for_order
Feature: Order payment method integrity
    In order to have valid payment method
    As a Customer
    I want to have valid order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And I am a logged in customer

    @ui @api
    Scenario: Preventing customer from completing checkout with no longer available payment method
        Given I have product "PHP T-Shirt" in the cart
        And I have proceeded selecting "Offline" payment method
        But the payment method "Offline" is disabled
        When I confirm my order
        Then I should be informed that this payment method has been disabled
        And I should not see the thank you page
