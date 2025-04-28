@receiving_discount
Feature: Applying promotion with an expiration date
    In order to pay proper amount after using the promotion
    As a Customer
    I want to have promotion's discounts applied to my cart only if the given promotion is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Christmas sale"
        And this promotion gives "$10.00" discount to every order
        And I am a logged in customer

    @api @ui
    Scenario: Receiving a discount from an ongoing promotion
        Given this promotion is valid until tomorrow
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Receiving no discount from an expired promotion
        Given this promotion has already expired
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving a discount from a promotion that has already started
        Given this promotion started yesterday
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @api @ui
    Scenario: Receiving no discount from a promotion that has not started yet
        Given this promotion starts tomorrow
        And I added product "PHP T-Shirt" to the cart
        When I check the details of my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied
