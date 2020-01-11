@managing_orders
Feature: Seeing payment state as paid after checkout steps if order total is zero
    In order to have coherent payment states of all orders
    As an Administrator
    I want orders with no unpaid payments to have payment state paid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store ships everywhere for free
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.00" discount to every order with quantity at least 1
        And there is a customer "lucy@teamlucifer.com" that placed an order "#00000666"
        And the customer bought a single "Angel T-Shirt"
        And the customer "Lucifer Morningstar" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method
        And I am logged in as an administrator

    @ui
    Scenario: Seeing payment state as paid on orders list
        When I browse orders
        Then the order "#00000666" should have order payment state "Paid"

    @ui
    Scenario: Seeing payment state as paid on order's summary
        When I view the summary of the order "#00000666"
        Then I should be informed that there are no payments
        And I should not be able to refund this payment
