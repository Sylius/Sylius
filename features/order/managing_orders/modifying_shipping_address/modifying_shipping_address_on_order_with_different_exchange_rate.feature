@modifying_shipping_address
Feature: Modifying a customer's shipping address of an order with a different currency
    In order to ship an order to a correct place
    As an Administrator
    I want to be able to modify a customer's shipping address without changing an order's total

    Background:
        Given the store operates on a channel named "Web"
        And the store ships to "United States"
        And the store has a zone "English" with code "EN"
        And this zone has the "United States" country member
        And that channel allows to shop using the "USD" currency
        And that channel allows to shop using the "GBP" currency with exchange rate 3.0
        And that channel uses the "USD" currency by default
        And the store allows paying with "Cash on Delivery"
        And the store has "DHL" shipping method with "$20.00" fee within the "EN" zone
        And the store has a product "Suit" priced at "$400.00"
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer has chosen to order in the "GBP" currency
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States" with identical billing address
        And the customer chose "DHL" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Modifying a customer's shipping address when the exchange rate has been changed
        Given the exchange rate for currency "GBP" was changed to 2.00
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "£1,260.00"
