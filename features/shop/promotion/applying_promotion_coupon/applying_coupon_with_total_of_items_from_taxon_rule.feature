@applying_promotion_coupon
Feature: Applying a coupon with a total of items from taxon rule
    In order to get predictable discounts on a coupon scoped to a taxon
    As a Customer
    I want the taxon total rule to count other promotions but not its own, and only items from the configured taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$50.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$30.00"
        And it belongs to "Mugs"
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api @ui
    Scenario: Not applying the taxon coupon when another promotion reduced the taxon items below the threshold
        Given there is a promotion "Automatic sale"
        And this promotion gives "$15.00" discount to every order
        And the store has promotion "T-Shirts coupon" with coupon "STACK10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$40.00"
        And I added product "PHP T-Shirt" to the cart
        And I applied the coupon with code "STACK10"
        When I check the details of my cart
        Then my cart total should be "$35.00"
        And my discount should be "-$15.00"

    @api @ui
    Scenario: Applying the taxon coupon when the taxon items stay above the threshold after the other promotion
        Given there is a promotion "Automatic sale"
        And this promotion gives "$15.00" discount to every order
        And the store has promotion "T-Shirts coupon" with coupon "STACK10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$40.00"
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "STACK10"
        When I check the details of my cart
        Then my cart total should be "$75.00"
        And my discount should be "-$25.00"

    @api @ui
    Scenario: Not counting items from other taxons towards the taxon rule threshold
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$60.00"
        And I added product "PHP T-Shirt" to the cart
        And I added 2 products "PHP Mug" to the cart
        And I applied the coupon with code "TSHIRTS10"
        When I check the details of my cart
        Then my cart total should be "$110.00"
        And there should be no discount applied

    @api @no-ui
    Scenario: Being notified that the taxon coupon is invalid when the taxon items do not meet the minimum
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$60.00"
        And I added product "PHP T-Shirt" to the cart
        When I use coupon with code "TSHIRTS10"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$50.00"
        And there should be no discount applied
