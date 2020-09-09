@shopping_cart
Feature: Seeing order items in cart
    In order to see details about my order items
    As a Visitor
    I want to be able to see my cart order items

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"

    @api
    Scenario: Viewing content of my cart
        When I add 5 of them to my cart
        And I check items in my cart
        Then my cart should have 5 items of product "T-shirt banana"
