@checkout
Feature: Order promotion coupon integrity
    In order to be able to place an order
    As a Customer
    I want to have a promotion coupon that is no longer valid removed from my order

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying Offline
        And the store ships everywhere for Free
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And this coupon can be used once
        And I am a logged in customer

    @api @ui
    Scenario: Being informed that the promotion is no longer applied when the coupon has reached its usage limit
        Given I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I chose "Free" shipping method and "Offline" payment method
        And I applied the coupon with code "SANTA2016"
        And this coupon has already reached its usage limit
        When I try to confirm my order
        Then I should be informed that this promotion is no longer applied
        And I should not see the thank you page

    @api @ui
    Scenario: Placing an order after the coupon has reached its usage limit
        Given I added product "PHP T-Shirt" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I chose "Free" shipping method and "Offline" payment method
        And I applied the coupon with code "SANTA2016"
        And this coupon has already reached its usage limit
        And I have tried to confirm my order
        When I confirm my order
        Then I should see the thank you page
