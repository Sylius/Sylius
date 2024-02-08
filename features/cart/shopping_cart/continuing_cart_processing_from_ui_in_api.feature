@shopping_cart
Feature: Continuing to process the cart picked up in the web store, in the API
    In order to order an item easier
    As a Customer
    I want to be able to start an order using UI and continue it via API

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Apple's Polishing Cloth"
        And I am a logged in customer on the web store and in the API

    @hybrid
    Scenario: Picking up the shopping cart on the web store and resuming it using API
        When I add "Apple's Polishing Cloth" to the cart on the web store
        And I check items in my cart using API
        Then there should be one item named "Apple's Polishing Cloth" in my cart
