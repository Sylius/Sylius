@receiving_discount
Feature: Receiving discount from a promotion with multiple actions
    In order to pay less while buying goods
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And there is a promotion "Christmas promotion"

    @ui
    Scenario: Receiving fixed discounts only on items that fit action filters
        Given this promotion gives "$10.00" off on every product with minimum price at "$50.00"
        And this promotion gives another "$5.00" off on every product classified as "T-Shirts"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "$15.00"
        And product "PHP Mug" price should not be decreased
        And my cart total should be "$105.00"

    @ui
    Scenario: Receiving percentage discounts only on items that fit action filters
        Given this promotion gives "20%" off on every product priced between "$30.00" and "$150.00"
        And this promotion gives another "10%" off every product classified as "T-Shirts"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then product "PHP T-Shirt" price should be decreased by "$30.00"
        And product "PHP Mug" price should not be decreased
        And my cart total should be "$90.00"
