@checkout
Feature: Preventing adding to cart invalid products
    In order to have correct products in cart when adding them
    As a customer
    I want to have the added products validated

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And this product's price is "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline
        And I am a logged in customer

    @api
    Scenario: Preventing customer from adding disabled product
        Given the product "PHP T-Shirt" has been disabled
        When I pick up my cart
        And I try to add product "PHP T-Shirt" to the cart
        Then I should be informed that product "PHP T-Shirt" is disabled
