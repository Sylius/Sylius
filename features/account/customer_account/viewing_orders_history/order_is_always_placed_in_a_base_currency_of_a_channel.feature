@customer_account
Feature: Order is always placed in a base currency of a channel
    In order to pay exact amount of money
    As an Customer
    I want to be charged in a base currency of channel

    Background:
        Given the store operates on a channel named "United States" in "USD" currency
        And that channel allows to shop using the "CAD" currency
        And the store ships to "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "US" zone
        And the store has a product "Angel T-Shirt" priced at "$20.00"
        And I am a logged in customer

    @ui
    Scenario: Placing an order with other than base display currency
        Given I changed my currency to "CAD"
        And I had product "Angel T-Shirt" in the cart
        And I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Cash on Delivery" payment
        When I confirm my order
        And I am viewing the summary of my last order
        Then I should see "$40.00" as order's total
        And I should see "$20.00" as order's subtotal
        And I should see "$20.00" as item price
        And I should see "$40.00" as payment total
