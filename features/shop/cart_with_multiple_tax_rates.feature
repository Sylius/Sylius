@ui-cart
Feature: Cart with multiple tax rates
    In order to order products with different tax rate
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And tax rate "EU VAT" with 23% rate belongs to "Taxable Goods" category
        And tax rate "X VAT" with 5% rate belongs to "Low-taxed Goods" category
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00 with "Taxable Goods" as tax category
        And catalog has a product "Symfony T-Shirt" priced at 50.00 with "Low-taxed Goods" as tax category
        And store has free shipping method
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for different taxed products
        Given I added product "PHP T-Shirt" and "Symfony T-Shirt" to the cart
        Then my cart taxes should be "€25.50"
        And my cart total should be "€175.50"

    Scenario: Proper taxes for multiple products with different tax rate
        Given I added 3 products "PHP T-Shirt" to the cart
        And I added 4 products "Symfony T-Shirt" to the cart
        Then my cart taxes should be "€79.00"
        And my cart total should be "€579.00"
