@receiving_discount
Feature: Applying promotion with an expiration date
    In order to pay proper amount after using the promotion
    As a Visitor
    I want to have promotion's discounts applied to my cart only if the given promotion is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And this promotion gives "$10.00" discount to every order

    @ui
    Scenario: Receiving a discount from a promotion which does not expire
        Given this promotion expires tomorrow
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving no discount from a valid but expired promotion
        Given this promotion has already expired
        When I add product "PHP T-Shirt" to the cart
        And my cart total should be "$100.00"
        And there should be no discount

    @ui
    Scenario: Receiving a discount from a promotion which has already started
        Given this promotion has started yesterday
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving no discount from a promotion that has not been started yet
        Given this promotion starts tomorrow
        When I add product "PHP T-Shirt" to the cart
        And my cart total should be "$100.00"
        And there should be no discount
