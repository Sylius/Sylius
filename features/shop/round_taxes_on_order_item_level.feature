@ui-cart
Feature: Round taxes on order item level
    In order to avoid taxes amount inaccuracy
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single channel
        And there is "EU" zone containing all members of European Union
        And default currency is "EUR"
        And default tax zone is "EU"
        And there is user "john@example.com" identified by "password123"
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has "Low VAT" tax rate of 10% for "Mugs" within "EU" zone
        And store has a product "PHP T-Shirt" priced at "€10.10"
        And store has a product "Symfony Mug" priced at "€45.95"
        And store has a product "PHP Mug" priced at "€45.94"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And product "Symfony Mug" belongs to "Mugs" tax category
        And product "PHP Mug" belongs to "Mugs" tax category
        And I am logged in as "john@example.com"

    Scenario: Properly rounded up tax for single product
        When I add product "Symfony Mug" to the cart
        Then my cart total should be "€50.55"
        And my cart taxes should be "€4.60"

    Scenario: Properly rounded down tax for single product
        When I add product "PHP Mug" to the cart
        Then my cart total should be "€50.53"
        And my cart taxes should be "€4.59"

    Scenario: Properly rounded taxes for multiple products
        When I add 10 products "PHP T-Shirt" to the cart
        Then my cart total should be "€124.23"
        And my cart taxes should be "€23.23"
