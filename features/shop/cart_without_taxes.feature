@ui-cart
Feature: Cart without taxes
    In order to order products without taxes
    As a Customer
    I want to be aware of taxes applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00 with no tax category
        And store has free shipping method
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for untaxed product
        Given I added product "PHP T-Shirt" to the cart
        Then my cart taxes should be "€0.00"
        And my cart total should be "€100.00"

    Scenario: Proper taxes for multiple untaxed products
        Given I added 3 products "PHP T-Shirt" to the cart
        Then my cart taxes should be "€0.00"
        And my cart total should be "€300.00"
