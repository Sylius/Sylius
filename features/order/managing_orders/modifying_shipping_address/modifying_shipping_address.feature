@modifying_address
Feature: Modifying a customer shipping address after an order has been placed
    In order to ship an order to a correct place
    As an Administrator
    I want to be able to modify a customer's shipping address after an order has been placed

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Suit" priced at "$400.00"
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States"
        And the customer set the billing address as "Mike Ross", "350 5th Ave", "10118", "New York", "United States"
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Modifying a customer's shipping address
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"

    @ui
    Scenario: Modifying a customer's shipping address when a product's price has been changed
        Given the product "Suit" changed its price to "$300.00"
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "$400.00"

    @ui
    Scenario: Modifying a customer's shipping address when a channel has been disabled
        Given the channel "Web" has been disabled
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "$400.00"

    @ui
    Scenario: Modifying a customer's shipping address when the currency has been disabled
        Given the currency "USD" has been disabled
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "$400.00"

    @ui
    Scenario: Modifying a customer's shipping address when the product is out of stock
        Given the product "Suit" is out of stock
        When I view the summary of the order "#00000001"
        And I want to modify a customer's shipping address of this order
        And I specify their shipping address as "Los Angeles", "Seaside Fwy", "90802", "United States" for "Lucifer Morningstar"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this order should be shipped to "Lucifer Morningstar", "Seaside Fwy", "90802", "Los Angeles", "United States"
        And the order's total should still be "$400.00"
