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
        And I am a logged in customer

    @api @ui
    Scenario: Receiving percentage discount for my cart
        Given I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @api @ui
    Scenario: Receiving percentage discount does not affect the shipping fee
        Given the store has "DHL" shipping method with "$10.00" fee
        And I added product "PHP T-Shirt" to the cart
        And I addressed the cart
        And I chose "DHL" shipping method
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my cart shipping total should be "$10.00"
        And my discount should be "-$20.00"

    @api @ui
    Scenario: Receiving percentage discount is correct for two items with different price
        Given the store has a product "Vintage Watch" priced at "$1,000.00"
        And I added product "PHP T-Shirt" to the cart
        And I added product "Vintage Watch" to the cart
        When I check the details of my cart
        Then my cart total should be "$880.00"
        And my discount should be "-$220.00"

    @api @ui
    Scenario: Receiving percentage discount is proportional to items values
        Given the store has a product "Symfony T-Shirt" priced at "$100.00"
        And I added 11 products "PHP T-Shirt" to the cart
        And I added product "Symfony T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$960.00"
        And my discount should be "-$240.00"
