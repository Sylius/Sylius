@checkout
Feature: Preventing adding to cart disabled products
    In order to have correct products in cart when adding them
    As a Customer
    I want to have the added products validated

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And I am a logged in customer

    @api
    Scenario: Preventing customer from adding disabled product
        Given the product "PHP T-Shirt" has been disabled
        When I pick up my cart
        And I try to add product "PHP T-Shirt" to the cart
        Then I should be informed that product "PHP T-Shirt" does not exist

    @api
    Scenario: Preventing customer from adding disabled variant
        Given the "Large" product variant is disabled
        When I pick up my cart
        And I try to add "Large" product variant
        Then I should be informed that "Large" product variant does not exist
