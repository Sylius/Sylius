@applying_taxes
Feature: Apply correct taxes for an order with a discount applied for all items in it
    In order to pay proper amount when buying goods
    As a Visitor
    I want to have correct taxes applied to my order with a discount

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
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

    @ui
    Scenario: Properly rounded up tax for a single product
        When I add product "Symfony Mug" to the cart
        Then my cart total should be "$50.55"
        And my cart taxes should be "$4.60"

    @ui
    Scenario: Properly rounded down tax for a single product
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$50.53"
        And my cart taxes should be "$4.59"

    @ui
    Scenario: Properly rounded taxes for multiple products with different tax rate
        When I add 2 products "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then my cart total should be "$74.79"
        And my cart taxes should be "$8.85"

    @ui
    Scenario: Properly rounded taxes for multiple products with the same tax rate
        When I add 2 products "Symfony Mug" to the cart
        And I add product "PHP Mug" to the cart
        Then my cart total should be "$162.73"
        And my cart taxes should be "$14.79"

    @ui
    Scenario: Properly rounded taxes for order with multiple promotions and multiple products with different tax rate
        And there is a promotion "Clothing promotion"
        And the promotion gives "$5.00" off if order contains a "PHP T-Shirt" product
        When I add product "Symfony Mug" to the cart
        And I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$57.04"
        And my cart taxes should be "$6.09"
