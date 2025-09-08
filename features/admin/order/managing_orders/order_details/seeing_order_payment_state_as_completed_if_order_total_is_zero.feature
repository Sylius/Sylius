@managing_orders
Feature: Seeing payment state as paid after checkout steps if order total is zero
    In order to have coherent payment states of all orders
    As an Administrator
    I want orders with no unpaid payments to have payment state paid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store ships everywhere for Free
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.00" discount to every order with quantity at least 1
        And the customer logged in
        And the customer added product "Angel T-Shirt" to the cart
        And the customer addressed the cart
        And the customer chose "Free" shipping method
        And the customer confirmed the order
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing payment state as paid on orders list
        When I browse orders
        Then the last order should have order payment state "Paid"

    @api @no-ui
    Scenario: Seeing payment state as paid on order's summary
        When I view the summary of the last order
        Then I should be informed that there are no payments

    @no-api @ui
    Scenario: Seeing payment state as paid on order's summary
        When I view the summary of the last order
        Then I should be informed that there are no payments
        And I should not be able to refund this payment
