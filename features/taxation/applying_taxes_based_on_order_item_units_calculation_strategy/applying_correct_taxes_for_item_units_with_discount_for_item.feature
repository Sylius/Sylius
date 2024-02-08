@applying_taxes
Feature: Applying correct taxes for item units with a discount
    In order to pay proper amount when buying goods
    As a Visitor
    I want to have correct taxes applied to my order when it has a discount

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store uses the "Order item units based" tax calculation strategy
        And the store has "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$56.05"
        And it belongs to "Mugs" tax category
        And the store has a product "PHP Mug" priced at "$56.04"
        And it belongs to "Mugs" tax category
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.10" off on a "Symfony Mug" product
        And there is a promotion "PHP promotion"
        And the promotion gives "$10.10" off on a "PHP Mug" product

    @ui @api
    Scenario: Applying correct taxes for a single item unit with discount
        When I add product "Symfony Mug" to the cart
        Then my cart total should be "$50.55"
        And my cart taxes should be "$4.60"

    @ui @api
    Scenario: Applying correct taxes for a single item unit with discount
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$50.53"
        And my cart taxes should be "$4.59"

    @ui @api
    Scenario: Applying correct taxes for multiple units of different products with discount and default calculator
        When I add 2 products "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        Then my cart total should be "$125.70"
        And my cart taxes should be "$13.80"

    @ui @api
    Scenario: Applying correct taxes for multiple units of different products with discount and decimal calculator
        Given the "US VAT" tax rate has decimal calculator configured
        And the "Low VAT" tax rate has decimal calculator configured
        When I add 2 products "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Mug" to the cart
        Then my cart total should be "$125.69"
        And my cart taxes should be "$13.79"
