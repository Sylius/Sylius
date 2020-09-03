@shopping_cart
Feature: Viewing a cart summary
    In order to see details about my order
    As a visitor
    I want to be able to see my cart summary

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"
        And there are 10 units of product "T-shirt banana" available in the inventory

    @api
    Scenario: Viewing content of my cart
        When I add 5 of them to my cart
        And I check items in my cart
        Then my cart should have 5 items of product "T-shirt banana"
