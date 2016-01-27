@ui-cart
Feature: Apply correct shipping fee on order
    In order to pay proper amount when buying goods and choosing paid shipment
    As a Customer
    I want to have correct shipping fees applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has "DHL" shipping method with "€10.00" fee
        And there is user "john@example.com" identified by "password123"
        And I am logged in as "john@example.com"

    Scenario: Adding proper shipping fee
        Given I am logged in as "john@example.com"
        And I have product "PHP T-Shirt" in the cart
        When I proceed selecting "DHL" shipping method
        Then my cart total should be "€110.00"
        And my cart shipping fee should be "€10.00"
