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

    @api @ui @mink:chromedriver
    Scenario: Receiving a discount from an ongoing promotion
        Given this promotion is valid until tomorrow
        When I add the product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui @mink:chromedriver
    Scenario: Receiving no discount from an expired promotion
        Given this promotion has already expired
        When I add the product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount applied

    @api @ui @mink:chromedriver
    Scenario: Receiving a discount from a promotion that has already started
        Given this promotion started yesterday
        When I add the product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui @mink:chromedriver
    Scenario: Receiving no discount from a promotion that has not started yet
        Given this promotion starts tomorrow
        When I add the product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount applied
