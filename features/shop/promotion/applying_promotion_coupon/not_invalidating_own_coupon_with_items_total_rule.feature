@applying_promotion_coupon
Feature: Not invalidating own coupon with items total rule
    In order to keep my legitimately applied discount on my cart
    As a Customer
    I want the items total rule to not count its own promotion's discount when revalidating the coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$22.00"
        And the store has promotion "Black Friday" with coupon "BUGME10"
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api @no-ui
    Scenario: Keeping the order fixed discount when the coupon is re-sent on an already discounted cart
        Given this promotion gives "$10.00" discount to every order with items total at least "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        When I use coupon with code "BUGME10"
        And I use coupon with code "BUGME10"
        Then I should not be notified that the coupon is invalid
        And my cart total should be "$34.00"
        And my discount should be "-$10.00"

    @api @no-ui
    Scenario: Keeping the order percentage discount when the coupon is re-sent on an already discounted cart
        Given this promotion gives "25%" discount to every order with items total at least "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        When I use coupon with code "BUGME10"
        And I use coupon with code "BUGME10"
        Then I should not be notified that the coupon is invalid
        And my cart total should be "$33.00"
        And my discount should be "-$11.00"

    @api @no-ui
    Scenario: Keeping the unit percentage discount when the coupon is re-sent on an already discounted cart
        Given this promotion gives "25%" off on every product when the item total is at least "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        When I use coupon with code "BUGME10"
        And I use coupon with code "BUGME10"
        Then I should not be notified that the coupon is invalid
        And my cart total should be "$33.00"
        And my total discount should be "-$11.00"

    @api @ui
    Scenario: Receiving the discount on the first application within the bug window
        Given this promotion gives "$10.00" discount to every order with items total at least "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "BUGME10"
        When I check the details of my cart
        Then my cart total should be "$34.00"
        And my discount should be "-$10.00"

    @api @no-ui
    Scenario: Dropping the discount when the cart genuinely falls below the threshold
        Given this promotion gives "$10.00" discount to every order with items total at least "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "BUGME10"
        And I changed product "PHP T-Shirt" quantity to 1 in my cart
        When I check the details of my cart
        Then my cart total should be "$22.00"
        And there should be no discount applied
