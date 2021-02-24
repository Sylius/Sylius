@receiving_discount
Feature: Receiving fixed discount on cart
    In order to pay proper amount while buying promoted goods
    As a Visitor
    I want to have promotions applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a product "PHP Mug" priced at "$6.00"

    @ui
    Scenario: Receiving fixed discount for my cart
        Given there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving fixed discount equal to the items total of my cart
        Given there is a promotion "Christmas Sale"
        And it gives "$106.00" discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        Then my cart total should be "$0.00"
        And my discount should be "-$106.00"

    @ui
    Scenario: Receiving fixed discount equal to the items total of my cart even if the discount is bigger than the items total
        Given there is a promotion "Thanksgiving sale"
        And it gives "$200.00" discount to every order
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$0.00"
        And my discount should be "-$100.00"

    @ui
    Scenario: Receiving fixed discount does not affect the shipping fee
        Given the store has "DHL" shipping method with "$10.00" fee
        And there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order
        And I am a logged in customer
        When I add product "PHP T-Shirt" to the cart
        And I proceed selecting "DHL" shipping method
        Then my cart total should be "$100.00"
        And my cart shipping total should be "$10.00"
        And my discount should be "-$10.00"
