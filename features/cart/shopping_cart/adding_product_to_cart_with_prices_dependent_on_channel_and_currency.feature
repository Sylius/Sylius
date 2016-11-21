@shopping_cart
Feature: Adding a product to cart with prices dependent on a channel and a currency
    In order to buy products in correct prices
    As a Customer
    I want to add products to my cart with prices dependent on visited channel and selected currency
    
    Background:
        Given the store has currency "EUR"
        And the store has currency "GBP" with exchange rate 0.7
        And the store operates on a channel named "Web-EU" in currency "EUR"
        And that channel allows to shop using "EUR" and "GBP" currencies
        And the store operates on another channel named "Web-GB"
        And that channel allows to shop using "EUR" and "GBP" currencies
        And the store has a product "Leprechaun's Gold" priced at "€12.54" available in channel "Web-EU" and channel "Web-GB"
        And it has different prices for different channels and currencies
        And it has price "€10.00" for "Web-EU" channel and "EUR" currency
        And it has price "£15.00" for "Web-EU" channel and "GBP" currency
        And it has price "£8.00" for "Web-GB" channel and "GBP" currency
        And I am a logged in customer

    @ui
    Scenario: Buying a product in default currency for browsed channel
        Given I change my current channel to "Web-EU"
        When I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "€10.00" in my cart

    @ui
    Scenario: Buying a product with proper price after currency change
        Given I change my current channel to "Web-EU"
        When I switch to the "GBP" currency
        And I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£15.00" in my cart

    @ui
    Scenario: Buying a product with proper price after channel change
        Given I change my current channel to "Web-GB"
        When I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£8.00" in my cart

    @ui
    Scenario: Buying a product with variant price if a price for selected channel and currency is not configured
        Given I change my current channel to "Web-GB"
        When I switch to the "EUR" currency
        And I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£12.54" in my cart
