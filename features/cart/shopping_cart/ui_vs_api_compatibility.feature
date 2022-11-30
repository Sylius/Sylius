@shopping_cart
Feature: Continuing to place an order from UI in API
    In order to place an order easily
    As a Customer
    I want to be able to make an order on UI and resume on API

    Background:
        Given the store has currency "EUR"
        And the store operates on a channel named "Web-EU" in "EUR" currency and with hostname "web-eu"
        And the store has a product "T-Shirt banana" priced at "€10.00" in "Web-EU" channel
        And I am a logged in customer on the API and the UI

    @api
    Scenario: Continuing to place an order from UI in API
        Given I add this product to the cart on the UI
        When I pick up my cart in the API
        Then I should be able to continue placing the same order in the API
