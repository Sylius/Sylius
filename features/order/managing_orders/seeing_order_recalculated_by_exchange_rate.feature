@managing_orders
Feature: Seeing an order recalculated by exchange rate
    In order to be certain about price of an order in different currency than default
    As an Administrator
    I want to see an order recalculated by exchange rate from this currency

    Background:
        Given the store ships to "British Virgin Islands"
        And the store has a zone "English" with code "EN"
        And this zone has the "British Virgin Islands" country member
        And the store operates on a channel named "Web"
        And that channel allows to shop using the "USD" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 3.0
        And that channel uses the "USD" currency by default
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within "EN" zone
        And the store has a product "Angel T-Shirt" priced at "$20.00"
        And there is a customer "No Face" identified by an email "lucy@teamlucifer.com" and a password "pswd"
        And the customer chose "GBP" currency
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "No Face" addressed it to "Lucifer Morningstar", "Seaside Fwy" "90802" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Seeing order summary in a currency selected by customer
        When I view the summary of the order "#00000666"
        Then the order's items total should be "£60.00"
        And the order's total should be "£120.00"
