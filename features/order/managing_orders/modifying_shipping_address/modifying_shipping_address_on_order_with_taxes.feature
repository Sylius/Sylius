@modifying_address
Feature: Modifying a customer's shipping address on an order with taxes
    In order to ship an order to a correct place
    As an Administrator
    I want to be able to modify a customer's shipping address without changing the order's total

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everything for free within the "US" zone
        And the store allows paying offline
        And the store has "VAT" tax rate of 20% for "Suits" within the "US" zone
        And the store has a product "Suit" priced at "$400.00"
        And it belongs to "Suits" tax category
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States"
        And the customer set the billing address as "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
        And the customer chose "Free" shipping method with "Offline" payment
        And I am logged in as an administrator

    @ui
    Scenario: Modifying a customer's shipping address when the applied promotion is no longer valid
        Given the "VAT" tax rate has changed to 10%
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "$480.00"
        And the order's tax total should still be "$80.00"
