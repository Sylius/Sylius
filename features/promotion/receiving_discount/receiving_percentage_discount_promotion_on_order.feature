@receiving_discount
Feature: Receiving percentage discount promotion on order
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"
        And it gives "20%" discount to every order

    @ui
    Scenario: Receiving percentage discount for my cart
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving percentage discount does not affect the shipping fee
        Given the store has "DHL" shipping method with "$10.00" fee
        And I am a logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$90.00"
        And my cart shipping total should be "$10.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving percentage discount is correct for two items with different price
        Given the store has a product "Vintage Watch" priced at "$1000.00"
        When I add product "PHP T-Shirt" to the cart
        And I add product "Vintage Watch" to the cart
        Then my cart total should be "$880.00"
        And my discount should be "-$220.00"

    @ui
    Scenario: Receiving percentage discount is proportional to items values
        Given the store has a product "Symfony T-Shirt" priced at "$100.00"
        When I add 11 products "PHP T-Shirt" to the cart
        And I add product "Symfony T-Shirt" to the cart
        Then my cart total should be "$960.00"
        And my discount should be "-$240.00"
