@shopping_cart
Feature: Viewing a cart summary
    In order to see details about my order
    As a Customer
    I want to be able to see my cart summary

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer

    @api @ui
    Scenario: Viewing information about empty cart
        When I see the summary of my cart
        Then my cart should be empty

    @no-api @ui
    Scenario: Viewing information about empty cart after clearing cookies
        Given the store has a product "T-Shirt banana" priced at "$12.54"
        And I added product "T-Shirt banana" to the cart
        When I am on the summary of my cart page
        And I've been gone for a long time
        And I try to proceed to the checkout
        Then I should see an empty cart
