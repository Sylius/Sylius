@ui-cart
Feature: Apply different taxes for variants with different tax category
    In order to pay proper amount when buying goods with variants from different tax categories
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And there is "EU" zone containing all members of European Union
        And default currency is "EUR"
        And default tax zone is "EU"
        And store has "EU VAT" tax rate of 23% for "Mugs" within "EU" zone
        And store has "Low VAT" tax rate of 5% for "Cheap Mugs" within "EU" zone
        And store has a product "PHP Mug" with "Medium Mug" variant priced at "€100.00" and "Large Mug" variant priced at "€50.00"
        And product variant "Medium Mug" belongs to "Cheap Mugs" tax category
        And product variant "Large Mug" belongs to "Mugs" tax category
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for different taxed variants
        When I add product "PHP Mug" to the cart, selecting "Medium Mug" variant
        And I add product "PHP Mug" to the cart, selecting "Large Mug" variant
        Then my cart total should be "€166.50"
        And my cart taxes should be "€16.50"
