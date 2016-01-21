@ui-cart
Feature: Cart with different tax rates for different time zones
    In order to order products with different tax rate for different zones
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And tax rate "EU VAT" with 23% rate belongs to "Taxable Goods" category for "EU" zone
        And tax rate "No tax" with 0% rate belongs to "Taxable Goods" category for "Rest of World" zone
        And tax rate "Low VAT" with 5% rate belongs to "Low-taxed Goods"
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00 with "Taxable Goods" as tax category
        And catalog has a product "Symfony Mug" priced at €50.00 with "Low-taxed Goods" as tax category
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
