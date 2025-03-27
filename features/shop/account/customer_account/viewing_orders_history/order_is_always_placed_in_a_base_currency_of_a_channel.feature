@customer_account
Feature: Order is always placed in a base currency of a channel
    In order to pay exact amount of money
    As an Customer
    I want to be charged in a base currency of channel

    Background:
        Given the store operates on a channel named "United States" in "USD" currency
        And that channel allows to shop using the "GBP" currency
        And the store ships to "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$20.00"
        And I am a logged in customer

    @no-api @ui
    Scenario: Placing an order with other than base display currency
        Given I changed my currency to "GBP"
        And I added product "Angel T-Shirt" to the cart
        And I addressed the cart
        When I proceed with "DHL" shipping method and "Cash on Delivery" payment
        And I confirm my order
        And I am viewing the summary of my last order
        Then I should see "£40.00" as order's total
        And I should see "£20.00" as order's subtotal
        And I should see "£20.00" as item price
        And I should see "$40.00" as payment total
