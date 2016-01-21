@ui-cart
Feature: Cart with single tax rate
    In order to order products with the same tax rate
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And store has "EU VAT" tax rate of 23% for "Clothes" in "EU" zone
        And catalog has a product "PHP T-Shirt" priced at €100.00
        And "PHP T-Shirt" tax category is "Clothes"
        And store has free shipping method
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for taxed product
        Given I added product "PHP T-Shirt" to the cart
        Then my cart taxes should be "€23.00"
        And my cart total should be "€123.00"

    Scenario: Proper taxes for multiple products with same tax rate
        Given I added 3 products "PHP T-Shirt" to the cart
        Then my cart taxes should be "€69.00"
        And my cart total should be "€369.00"
