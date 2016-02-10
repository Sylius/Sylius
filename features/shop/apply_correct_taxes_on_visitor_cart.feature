@ui-cart
Feature: Apply correct taxes on visitor cart
    In order to buy goods with correct taxes applied
    As a Visitor
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single channel
        And the store ships to "France" and "Australia"
        And there is "EU" zone containing all members of European Union
        And there is rest of the world zone containing all other countries
        And default currency is "EUR"
        And default tax zone is "EU"
        And the store has "EU VAT" tax rate of 23% for "Clothes" within "EU" zone
        And the store has "EU VAT" tax rate of 10% for "Clothes" for the rest of the world
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And product "PHP T-Shirt" belongs to "Clothes" tax category
        And the store ships everything for free to all available locations
        And the store allows paying offline

    Scenario: Proper taxes for taxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€123.00"
        And my cart taxes should be "€23.00"

    Scenario: Proper taxes after specifying shipping address
        Given I have product "PHP T-Shirt" in the cart
        When I proceed as guest "john@example.com" with "Australia" as shipping country
        Then my cart total should be "€110.00"
        And my cart taxes should be "€10.00"
