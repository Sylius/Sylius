@ui-cart
Feature: Cart with different tax rates for different time zones
    In order to order products with different tax rate for different zones
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And store has "EU VAT" tax rate of 23% for "Clothes" in "EU" zone
        And store has "No tax" tax rate of 0% for "Clothes" in "Rest of World" zone
        And store has "Low VAT" tax rate of 5% for "Mugs" in "EU" zone
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00
        And "PHP T-Shirt" tax category is "Clothes"
        And catalog has a product "Symfony Mug" priced at €50.00
        And "Symfony T-Shirt" tax category is "Mugs"
        And store has free shipping method
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes before addressing
        Given I added product "PHP T-Shirt" to the cart
        Then my cart taxes should be "€23.00"
        And my cart total should be "€123.00"

    Scenario: Proper taxes after specifying address
        Given I added product "PHP T-Shirt" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as address country
        Then my cart taxes should be "€0.00"
        And my cart total should be "€100.00"

    Scenario: Proper taxes for multiple products after specifying address
        Given I added 3 products "PHP T-Shirt" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as address country
        Then my cart taxes should be "€0.00"
        And my cart total should be "€300.00"

    Scenario: Proper taxes with rates-zones chaos before addressing
        Given I added product "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Mug" to the cart
        Then my cart taxes should be "€28.00"
        And my cart total should be "€228.00"

    Scenario: Proper taxes with rates-zones chaos after addressing
        Given I added product "PHP T-Shirt" to the cart
        And I added 2 products "Symfony Mug" to the cart
        And I proceed selecting "Offline" payment method and "Uzbekistan" as address country
        Then my cart taxes should be "€23.00"
        And my cart total should be "€223.00"
