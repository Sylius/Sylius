@checkout
Feature: Preventing adding to cart product or variant from another channel
    In order to have correct products in cart when adding them
    As a Customer
    I want to have the added products validated

    Background:
        Given the store operates on a channel named "France" with hostname "france"
        And the store has a "Baquette" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And the store has a "Pain" product
        And the store operates on a channel named "Poland" with hostname "poland"
        And I am a logged in customer

    @api
    Scenario: Preventing customer from adding simple product from another channel
        Given I am browsing channel "Poland"
        When I pick up my cart
        And I try to add product "Pain" to the cart
        Then I should be informed that product "Pain" does not exist

    @api
    Scenario: Preventing customer from adding product with variant from another channel
        Given I am browsing channel "Poland"
        When I pick up my cart
        And I try to add "Large" variant of product "Baquette"
        Then I should be informed that product "Baquette" does not exist
