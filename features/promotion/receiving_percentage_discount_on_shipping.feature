@promotion
Feature: Receiving percentage discount on shipping
    In order to pay decreased amount for shipping
    As a Customer
    I want to have shipping promotion applied to my cart

    Background:
        Given the store is operating on a single "France" channel
        And the store has "DHL" shipping method with "€10.00" fee
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And there is a promotion "Holiday promotion"
        And I am logged in customer

    @todo
    Scenario: Receiving percentage discount on shipping
        Given the promotion gives "20%" percentage discount on shipping to every order
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "€108.00"
        And my cart shipping fee should be "€10.00"
        And my discount should be "-€2.00"

    @todo
    Scenario: Receiving free shipping
        Given the promotion gives "100%" percentage discount on shipping to every order
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "€100.00"
        And my cart shipping fee should be "€10.00"
        And my discount should be "-€10.00"

    @todo
    Scenario: Not receiving percentage discount on shipping before selecting shipping method
        Given the promotion gives "100%" percentage discount on shipping to every order
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€100.00"
        And my cart shipping fee should be "€0.00"
        And my discount should be "€0.00"
