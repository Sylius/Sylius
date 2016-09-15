@customer_account
Feature: Viewing details of an order
    In order to check some details of my placed order
    As an Customer
    I want to be able to view details of my placed order

    Background:
        Given the store ships to "British Virgin Islands"
        And the store has a zone "English" with code "EN"
        And this zone has the "British Virgin Islands" country member
        And the store operates on a channel named "Web"
        And that channel allows to shop using the "USD" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 3.0
        And that channel uses the "USD" currency by default
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "Angel T-Shirt" priced at "$20.00"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I have chosen to order in the "GBP" currency
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "DHL" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Viewing basic information about an order
        When I view the summary of the order "#00000666"
        Then I should see "£120.00" as order's total
        And I should see "£60.00" as order's subtotal
        And I should see "£60.00" as item price
        And I should see "£120.00" as payment total
