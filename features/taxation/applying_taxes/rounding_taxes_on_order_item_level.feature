@applying_taxes
Feature: Round taxes on order item level
    In order to avoid taxes amount inaccuracy
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "US VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has "Low VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$10.10"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Mug" priced at "$45.95"
        And it belongs to "Mugs" tax category
        And the store has a product "PHP Mug" priced at "$45.94"
        And it belongs to "Mugs" tax category

    @ui
    Scenario: Properly rounded up tax for single product
        When I add product "Symfony Mug" to the cart
        Then my cart total should be "$50.55"
        And my cart taxes should be "$4.60"

    @ui
    Scenario: Properly rounded down tax for single product
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$50.53"
        And my cart taxes should be "$4.59"

    @ui
    Scenario: Properly rounded taxes for multiple products
        When I add 10 products "PHP T-Shirt" to the cart
        Then my cart total should be "$124.23"
        And my cart taxes should be "$23.23"
