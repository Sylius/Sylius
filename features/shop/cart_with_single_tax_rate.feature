@ui-cart
Feature: Cart with single tax rate
    In order to buy goods with correct taxes applied
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And there is "EU" zone containing all members of European Union
        And default currency is "EUR"
        And default zone is "EU"
        And there is user "john@example.com" identified by "password123", with "United Kingdom" as shipping country
        And store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has a product "Symfony Hat" priced at "€30.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And product "Symfony Hat" belongs to "Clothes" tax category
        And store ships everything for free
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€123.00"
        And my cart taxes should be "€23.00"

    Scenario: Proper taxes for multiple same products with the same tax rate
        When I add 3 products "PHP T-Shirt" to the cart
        Then my cart total should be "€369.00"
        And my cart taxes should be "€69.00"

    Scenario: Proper taxes for multiple different products with the same tax rate
        When I add 3 products "PHP T-Shirt" to the cart
        When I add 2 products "Symfony Hat" to the cart
        Then my cart total should be "€442.80"
        And my cart taxes should be "€82.80"
