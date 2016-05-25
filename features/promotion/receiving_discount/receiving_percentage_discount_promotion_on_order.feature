@receiving_discount
Feature: Receiving percentage discount promotion on order
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And there is a promotion "Holiday promotion"
        And it gives "20%" discount to every order

    @ui
    Scenario: Receiving percentage discount for my cart
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€80.00"
        And my discount should be "-€20.00"

    @ui
    Scenario: Receiving percentage discount does not affect the shipping fee
        Given the store has "DHL" shipping method with "€10.00" fee
        And I am logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "€90.00"
        And my cart shipping total should be "€10.00"
        And my discount should be "-€20.00"
