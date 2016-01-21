@ui-cart
Feature: Cart shipping without taxes
    In order to order with untaxed shipping method
    As a Customer
    I want to be aware of shipping fees applied on my order

    Background:
        Given that store is operating on the France channel
        And default currency is "EUR"
        And there is user "john@example.com" identified by "password123"
        And catalog has a product "PHP T-Shirt" priced at €100.00
        And store has "DHL" shipping method with "€10.00" fee
        And I am logged in as "john@example.com"


    Scenario: Adding proper shipping fee
        Given I am logged in as "john@example.com"
        And I added product "PHP T-Shirt" to the cart
        When I proceed selecting "DHL" shipping method
        Then my cart shipping fee should be "€10.00"
        And my cart total should be "€110.00"
