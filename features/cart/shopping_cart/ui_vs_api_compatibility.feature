@shopping_cart
Feature: Cart management in UI and API
    In order to order an item
    As a Customer
    I want to be able to make an order on UI and resume on API

    Background:
        Given the store has currency "EUR"
        And the store operates on a channel named "Web-EU" in "EUR" currency and with hostname "web-eu"
        And that channel allows to shop using the "EUR" currency
        And the store has a product "T-Shirt banana" priced at "€10.00" in "Web-EU" channel
        And I am a logged in customer on the API and the UI

    @api
    Scenario: Create a cart as a user, using it from the API
        When I add this product to the cart on the UI
        Then I pick up my cart from the API
        And I can continue the order on the API
