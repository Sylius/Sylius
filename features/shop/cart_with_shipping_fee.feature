@ui-cart
Feature: Cart shipping without taxes
    In order to buy goods with correct shipping fees applied
    As a Customer
    I want to have correct shipping fees applied to my order

    Background:
        Given the store is operating on a single "France" channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And store has a product "PHP T-Shirt" priced at "€100.00"
        And store has "DHL" shipping method with "€10.00" fee
        And store allows paying offline
        And I am logged in as "john@example.com"

    Scenario: Adding proper shipping fee
        Given I am logged in as "john@example.com"
        And I add product "PHP T-Shirt" to the cart
        When I proceed selecting "DHL" shipping method
        Then my cart total should be "€110.00"
        And my cart shipping fee should be "€10.00"
