@applying_taxes
Feature: Applying correct taxes for item units with a discount applied for all items in it
    In order to pay proper amount when buying goods
    As a Visitor
    I want to have correct taxes applied to my order with a discount

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
        And the promotion gives "$10.10" off if order contains a "Symfony Mug" product
        And there is a promotion "PHP promotion"
        And the promotion gives "$10.10" off if order contains a "PHP Mug" product

    @ui @api
    Scenario: Applying correct taxes for a single item unit with order discount
        When I add product "Symfony Mug" to the cart
        Then my cart total should be "$50.55"
        And my cart taxes should be "$4.60"

    @ui @api
    Scenario: Applying correct taxes for a single item unit with order discount
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$50.53"
        And my cart taxes should be "$4.59"

    @ui @api
    Scenario: Applying correct taxes for multiple units of different products with order discount
        When I add 2 products "Symfony Mug" to the cart
        And I add product "PHP Mug" to the cart
        Then my cart total should be "$162.73"
        And my cart taxes should be "$14.79"
