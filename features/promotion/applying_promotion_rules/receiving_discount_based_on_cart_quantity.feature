@applying_promotion_rules
Feature: Receiving discount based on cart quantity
    In order to pay decreased amount for my order during promotion
    As a Visitor
    I want to have promotion applied to my cart when my cart quantity qualifies

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"

    @ui
    Scenario: Receiving discount when buying more than required quantity
        Given the promotion gives "$10.00" discount to every order with quantity at least 3
        When I add 4 products "PHP T-Shirt" to the cart
        Then my cart total should be "$390.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving discount when buying the required quantity
        Given the promotion gives "$10.00" discount to every order with quantity at least 4
        When I add 4 products "PHP T-Shirt" to the cart
        Then my cart total should be "$390.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Not receiving discount when buying less than required quantity
        Given the promotion gives "$10.00" discount to every order with quantity at least 5
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$200.00"
        And there should be no discount

    @ui
    Scenario: Receiving discount when buying different products with the required quantity
        Given the store has a product "Symfony T-Shirt" priced at "$50.00"
        And the promotion gives "$10.00" discount to every order with quantity at least 3
        When I add 2 products "PHP T-Shirt" to the cart
        And I add 2 products "Symfony T-Shirt" to the cart
        Then my cart total should be "$290.00"
        And my discount should be "-$10.00"
