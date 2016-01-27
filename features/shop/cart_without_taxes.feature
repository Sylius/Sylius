@ui-cart
Feature: Cart without taxes
    In order to buy goods without taxes
    As a Customer
    I want to have correct taxes applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has a product "Symfony Mug" priced at "€30.00"
        And store ships everything for free
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Proper taxes for untaxed product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€100.00"
        And my cart taxes should be "€0.00"

    Scenario: Proper taxes for untaxed product with quantity specified
        When I add 3 products "PHP T-Shirt" to the cart
        Then my cart total should be "€300.00"
        And my cart taxes should be "€0.00"

    Scenario: Proper taxes for multiple untaxed products
        When I add product "PHP T-Shirt" to the cart
        And I add product "Symfony Mug" to the cart
        Then my cart total should be "€130.00"
        And my cart taxes should be "€0.00"
