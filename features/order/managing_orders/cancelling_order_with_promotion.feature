@managing_orders
Feature: Cancelling order with promotion applied
    In order to maintain correct order history
    As an Administrator
    I want to have a promotion still applied after cancelling order even if the promotion is no longer valid

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Suit" priced at "$400.00"
        And there is a promotion "Holiday promotion"
        And the promotion gives "$50" discount to every order
        And there is a customer "mike@ross.com" that placed an order "#00000001"
        And the customer bought a single "Suit"
        And the customer "Mike Ross" addressed it to "350 5th Ave", "10118" "New York" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Cancelling order when the applied promotion is no longer valid
        Given the promotion was disabled for the channel "Web"
        When I view the summary of the order "#00000001"
        And I cancel this order
        Then this order should have state "Cancelled"
        And the order's total should still be "$350.00"
        And the order's promotion total should still be "-$50.00"
