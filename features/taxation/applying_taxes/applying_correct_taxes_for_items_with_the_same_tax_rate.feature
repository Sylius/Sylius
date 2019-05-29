@applying_taxes
Feature: Apply correct taxes for items with the same tax rate
    In order to pay proper amount when buying goods from the same tax category
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store operates on a single channel in "United States"
        And default tax zone is "US"
        And the store has "VAT" tax rate of 23% for "Clothes" within the "US" zone
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "Clothes" tax category
        And the store has a product "Symfony Hat" priced at "$30.00"
        And it belongs to "Clothes" tax category

    @ui
    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$123.00"
        And my cart taxes should be "$23.00"

    @ui
    Scenario: Proper taxes for multiple same products with the same tax rate
        When I add 3 products "PHP T-Shirt" to the cart
        Then my cart total should be "$369.00"
        And my cart taxes should be "$69.00"

    @ui
    Scenario: Proper taxes for multiple different products with the same tax rate
        When I add 3 products "PHP T-Shirt" to the cart
        And I add 2 products "Symfony Hat" to the cart
        Then my cart total should be "$442.80"
        And my cart taxes should be "$82.80"

    @ui @javascript
    Scenario: Proper taxes after removing one of the item
        Given I have 3 products "PHP T-Shirt" in the cart
        And I have 2 products "Symfony Hat" in the cart
        When I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "$73.80"
        And my cart taxes should be "$13.80"

    @ui
    Scenario: Proper taxes after changing item quantity
        Given I have 3 products "PHP T-Shirt" in the cart
        And I have 2 products "Symfony Hat" in the cart
        When I change "PHP T-Shirt" quantity to 1
        Then my cart total should be "$196.80"
        And my cart taxes should be "$36.80"
