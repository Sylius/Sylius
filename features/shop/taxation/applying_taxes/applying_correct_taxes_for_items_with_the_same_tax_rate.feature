@applying_taxes
Feature: Apply correct taxes for items with the same tax rate
    In order to pay proper amount when buying goods from the same tax category
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Hat" priced at "$30.00"
        And it belongs to "Clothes" tax category
        And I am a logged in customer

    @api @ui
    Scenario: Proper taxes for taxed product
        Given I added product "PHP T-Shirt" to the cart
        When I check details of my cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @api @ui
    Scenario: Proper taxes for multiple same products with the same tax rate
        Given I added 3 products "PHP T-Shirt" to the cart
        When I check details of my cart
        Then my cart total should be "$369.00"
        And my cart taxes should be "$69.00"

    @api @ui
    Scenario: Proper taxes for multiple different products with the same tax rate
        Given I added 3 products "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Hat" to the cart
        When I check details of my cart
        Then my cart total should be "$442.80"
        And my cart taxes should be "$82.80"

    @api @ui @mink:chromedriver
    Scenario: Proper taxes after removing one of the item
        Given I added 3 products "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Hat" to the cart
        When I check details of my cart
        And I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "$73.80"
        And my cart taxes should be "$13.80"

    @api @ui @mink:chromedriver
    Scenario: Proper taxes after changing item quantity
        Given I added 3 products "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Hat" to the cart
        When I change product "PHP T-Shirt" quantity to 1 in my cart
        Then my cart total should be "$196.80"
        And my cart taxes should be "$36.80"
