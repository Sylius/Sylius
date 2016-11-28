@shopping_cart
Feature: Clearing cart after logging out
    In order to not allow to use my cart by anybody
    As a Customer
    I want to be able to have my cart cleared after logging out

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Stark T-shirt" priced at "$12.00"
        And I am a logged in customer
        And I have product "Stark T-Shirt" in the cart

    @ui
    Scenario: Clearing cart after logging out
        When I log out
        And I see the summary of my cart
        Then my cart should be empty
