@applying_promotion_coupon
Feature: Applying a coupon on an already discounted cart
    In order to have predictable discounts when several promotions apply
    As a Customer
    I want a coupon's items total rule to be evaluated against the price already reduced by other promotions

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$50.00"
        And there is a promotion "Automatic sale"
        And this promotion gives "$15.00" discount to every order
        And the store has promotion "Coupon sale" with coupon "STACK10"
        And this promotion gives "$10.00" discount to every order with items total at least "$40.00"
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api @ui
    Scenario: Not applying the coupon when another promotion reduced the cart below the threshold
        Given I added product "PHP T-Shirt" to the cart
        And I applied the coupon with code "STACK10"
        When I check the details of my cart
        Then my cart total should be "$35.00"
        And my discount should be "-$15.00"

    @api @ui
    Scenario: Applying the coupon when the cart stays above the threshold after the other promotion
        Given I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "STACK10"
        When I check the details of my cart
        Then my cart total should be "$75.00"
        And my discount should be "-$25.00"
