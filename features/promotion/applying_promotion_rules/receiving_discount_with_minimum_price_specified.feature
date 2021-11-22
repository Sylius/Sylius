@applying_promotion_rules
Feature: Applying promotion on a product with minimum price specified
    In order to pay the proper amount when the product's minimum price is specified
    As a Visitor
    I want to have promotion discounts applied to my cart, taking into account the product's minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts"
        And the store has a "T-Shirt" configurable product
        And it belongs to "T-Shirts"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the "PHP T-Shirt" variant has minimum price "$15.00" in "United States" channel

    @ui @api
    Scenario: Receiving fixed discount for my cart
        Given there is a promotion "T-Shirts promotion"
        And the promotion gives "$10.00" discount to every order with items total at least "$20.00"
        When I add product "T-Shirt" to the cart
        Then my cart total should be "$15.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving discount based on chosen product
        Given there is a promotion "T-Shirts promotion"
        And the promotion gives "$20.00" off if order contains a "T-Shirt" product
        When I add product "T-shirt" to the cart
        Then my cart total should be "$15.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving discount when buying more than required quantity
        Given there is a promotion "T-Shirts promotion"
        And the promotion gives "$50.00" discount to every order with quantity at least 2
        When I add 2 products "T-Shirt" to the cart
        Then my cart total should be "$30.00"
        And my discount should be "-$10.00"

    @ui @api
    Scenario: Receiving discount on order while buying product from promoted taxon which fits price criteria
        Given there is a promotion "T-Shirts promotion"
        And the promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$20.00"
        When I add product "T-Shirt" to the cart
        Then my cart total should be "$15.00"
        And my discount should be "-$5.00"
