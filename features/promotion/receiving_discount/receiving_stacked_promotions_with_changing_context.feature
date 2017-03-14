@receiving_discount
Feature: Receiving stacked promotion with changing context
    In order to pay proper amount while buying promoted goods
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "DHL" shipping method with "$10.00" fee
        And the store has a product "PHP T-Shirt" priced at "$120.00"
        And there is a promotion "Holiday promotion" with priority 1
        And it gives "50%" discount to every order
        And there is a promotion "Free shiping over" with priority 0
        And it gives free shipping to every order over "$100"

    @ui
    Scenario: Receiving only the "Holiday promotion"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$70.00"
        And my discount should be "-$60.00"
        And my cart shipping total should be "$10.00"

    @ui
    Scenario: Receiving the "Holiday promotion" and the free shipping discount
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$120.00"
        And my discount should be "-$120.00"
        And my cart shipping total should be "$0.00"
