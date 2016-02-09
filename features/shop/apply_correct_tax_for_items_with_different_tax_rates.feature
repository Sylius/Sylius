@ui-cart
Feature: Apply correct tax for items with different tax rates
    In order to pay proper amount when buying goods from different tax categories
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single channel
        And there is "EU" zone containing all members of European Union
        And default currency is "EUR"
        And default tax zone is "EU"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "Low VAT" tax rate of 5% for "Mugs" within "EU" zone
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And store has a product "Symfony Mug" priced at "€50.00"
        And product "Symfony Mug" belongs to "Mugs" tax category
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for different taxed products
        When I add product "PHP T-Shirt" to the cart
        And I add product "Symfony Mug" to the cart
        Then my cart total should be "€175.50"
        And my cart taxes should be "€25.50"

    Scenario: Proper taxes for multiple products with different tax rate
        When I add 3 products "PHP T-Shirt" to the cart
        And I add 4 products "Symfony Mug" to the cart
        Then my cart total should be "€579.00"
        And my cart taxes should be "€79.00"
