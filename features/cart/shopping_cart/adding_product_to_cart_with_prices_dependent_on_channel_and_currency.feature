@shopping_cart
Feature: Adding a product to cart with prices dependent on a channel and a currency
    In order to buy products in correct prices
    As a Customer
    I want to add products to my cart with prices dependent on visited channel and selected currency
    
    Background:
        Given the store has currency "Euro", "British Pound"
        And the store operates on a channel named "Web-EU"
        And that channel allows to shop using the "EUR" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 0.7
        And that channel uses the "EUR" currency by default
        And the store operates on a channel named "Web-GB"
        And that channel allows to shop using the "GBP" currency
        And that channel allows to shop using the "EUR" currency with exchange rate 1.5
        And that channel uses the "GBP" currency by default
        And the store has a product "Leprechaun's Gold" priced at "€12.54"
        And it has different prices for different channels and currencies
        And it has price "€10.00" for "Web-EU" channel and "EUR" currency
        And it has price "£15.00" for "Web-EU" channel and "GBP" currency
        And it has price "£8.00" for "Web-GB" channel and "GBP" currency
        And I am a logged in customer

    @ui @todo
    Scenario: Buying a product in default currency for browsed channel
        Given I browse the "Web-EU" channel
        When I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "€10.00" in my cart

    @ui @todo
    Scenario: Buying a product with proper price after currency change
        Given I browse the "Web-EU" channel
        When I switch to the "GBP" currency
        And I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£15.00" in my cart

    @ui @todo
    Scenario: Buying a product with proper price after channel change
        Given I browse the "Web-GB" channel
        When I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£8.00" in my cart

    @ui @todo
    Scenario: Buying a product with variant price if a price for selected channel and currency is not configured
        Given I browse the "Web-GB" channel
        When I switch to the "EUR" currency
        And I add product "Leprechaun's Gold" to the cart
        Then I should see "Leprechaun's Gold" with unit price "£12.54" in my cart
