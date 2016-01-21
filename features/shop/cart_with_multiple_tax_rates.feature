@ui-cart
Feature: Cart with multiple tax rates
    In order to order products with different tax rate
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And store has "EU VAT" tax rate of 23% for "Clothes"
        And store has "Low VAT" tax rate of 5% for "Mugs"
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00
        And "PHP T-Shirt" tax category is "Clothes"
        And catalog has a product "Symfony Mug" priced at 50.00
        And "Symfony T-Shirt" tax category is "Mugs"
        And store has free shipping method
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for different taxed products
        Given I added product "PHP T-Shirt" and "Symfony Mug" to the cart
        Then my cart taxes should be "€25.50"
        And my cart total should be "€175.50"

    Scenario: Proper taxes for multiple products with different tax rate
        Given I added 3 products "PHP T-Shirt" to the cart
        And I added 4 products "Symfony Mug" to the cart
        Then my cart taxes should be "€79.00"
        And my cart total should be "€579.00"
