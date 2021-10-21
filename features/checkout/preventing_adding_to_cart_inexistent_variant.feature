@checkout
Feature: Preventing adding to cart inexistent product variant
    In order to have correct products in cart when adding them
    As a Customer
    I want to have the added product variants validated

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And I am a logged in customer

    @api
    Scenario: Preventing customer from adding inexistent variant
        Given the "Large" product variant is disabled
        When I pick up my cart
        And I try to add product "Super Cool T-Shirt" with variant code "Magic"
        Then I should be informed that product variant with code "Magic" does not exist
