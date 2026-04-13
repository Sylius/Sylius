@applying_promotion_coupon
Feature: Applying coupon with "total price of items from taxon" rule based on original item prices
    In order to correctly apply a coupon with a minimum total requirement from a specific taxon
    As a Customer
    I want the coupon eligibility to be checked against the original unit prices, not discounted totals

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$25.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$15.00"
        And it belongs to "Mugs"
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api
    Scenario: Coupon remains valid when its own discount brings the items total below the minimum value threshold
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 3 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "TSHIRTS10"
        When I use coupon with code "TSHIRTS10"
        Then my cart total should be "$65.00"
        And my discount should be "-$10.00"

    @api
    Scenario: Coupon with taxon total rule can be applied when an automatic promotion already discounts items from that taxon
        Given there is a promotion "T-Shirts auto discount"
        And it gives "10%" off every product classified as "T-Shirts"
        And the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$5.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 3 products "PHP T-Shirt" to the cart
        When I use coupon with code "TSHIRTS10"
        Then my cart total should be "$62.50"
        And my discount should be "-$12.50"

    @api
    Scenario: Coupon remains valid after re-validation when cumulative discounts from overlapping promotions bring items total well below the threshold
        Given there is a promotion "T-Shirts auto discount"
        And it gives "10%" off every product classified as "T-Shirts"
        And the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$65.00"
        And I added 3 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "TSHIRTS10"
        When I use coupon with code "TSHIRTS10"
        Then my cart total should be "$57.50"
        And my discount should be "-$17.50"

    @api
    Scenario: Coupon with taxon total rule does not apply when items from the taxon genuinely do not meet the required minimum value
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 2 products "PHP T-Shirt" to the cart
        When I use coupon with code "TSHIRTS10"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$50.00"
        And there should be no discount applied

    @api
    Scenario: Coupon with taxon total rule applies only to items from the specified taxon, ignoring items from other taxons when checking the minimum value
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$5.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 2 products "PHP T-Shirt" to the cart
        And I added 3 products "PHP Mug" to the cart
        When I use coupon with code "TSHIRTS10"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$95.00"
        And there should be no discount applied

    @api @ui
    Scenario: Discount is correctly applied when a coupon with taxon total rule is used
        Given the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 3 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "TSHIRTS10"
        When I check the details of my cart
        Then my cart total should be "$65.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Discount from overlapping automatic and coupon promotions is correctly applied when taxon items meet the threshold
        Given there is a promotion "T-Shirts auto discount"
        And it gives "10%" off every product classified as "T-Shirts"
        And the store has promotion "T-Shirts coupon" with coupon "TSHIRTS10"
        And this promotion gives "$5.00" off if order contains products classified as "T-Shirts" with a minimum value of "$70.00"
        And I added 3 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "TSHIRTS10"
        When I check the details of my cart
        Then my cart total should be "$62.50"
        And my discount should be "-$12.50"
