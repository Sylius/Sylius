@receiving_discount
Feature: Receiving percentage discount on shipping
    In order to pay decreased amount for shipping
    As a Customer
    I want to have shipping promotion applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store has a product "PHP Mug" priced at "$20.00"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"
        And I am a logged in customer

    @ui
    Scenario: Receiving percentage discount on shipping
        Given the promotion gives "20%" discount on shipping to every order
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$108.00"
        And my cart shipping total should be "$8.00"

    @ui
    Scenario: Receiving free shipping
        Given the promotion gives free shipping to every order
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$100.00"
        And my cart shipping total should be "$0.00"

    @ui
    Scenario: Receiving free shipping after changing the quantity of a product in the cart
        Given the promotion gives free shipping to every order over "$70.00"
        When I add product "PHP Mug" to the cart
        And I change "PHP Mug" quantity to 4
        Then my cart total should be "$80.00"
        And my cart shipping total should be "$0.00"

    @ui
    Scenario: Not receiving free shipping after changing the quantity of a product in the cart
        Given the promotion gives free shipping to every order over "$70.00"
        When I add product "PHP Mug" to the cart
        And I change "PHP Mug" quantity to 4
        And I change "PHP Mug" quantity to 2
        Then my cart total should be "$50.00"
        And my cart shipping total should be "$10.00"

    @ui @javascript
    Scenario: Still receiving free shipping after removing the product from the cart
        Given the promotion gives free shipping to every order over "$70.00"
        When I add product "PHP Mug" to the cart
        And I add product "PHP T-Shirt" to the cart
        And I remove product "PHP Mug" from the cart
        Then my cart total should be "$100.00"
        And my cart shipping total should be "$0.00"

    @ui @javascript
    Scenario: Not receiving free shipping after removing the product from the cart
        Given the promotion gives free shipping to every order over "$70.00"
        When I add product "PHP Mug" to the cart
        And I add product "PHP T-Shirt" to the cart
        And I remove product "PHP T-Shirt" from the cart
        Then my cart total should be "$30.00"
        And my cart shipping total should be "$10.00"
