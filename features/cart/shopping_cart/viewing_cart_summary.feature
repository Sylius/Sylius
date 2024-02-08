@shopping_cart
Feature: Viewing a cart summary
    In order to see details about my order
    As a Visitor
    I want to be able to see my cart summary

    Background:
        Given the store operates on a single channel in "United States"

    @ui @api
    Scenario: Viewing information about empty cart
        When I see the summary of my cart
        Then my cart should be empty

    @ui @no-api
    Scenario: Viewing information about empty cart after clearing cookies
        Given the store has a product "T-Shirt banana" priced at "$12.54"
        And I added this product to the cart
        And I am on the summary of my cart page
        But I've been gone for a long time
        When I try to update my cart
        Then I should see an empty cart
