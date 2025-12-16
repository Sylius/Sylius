@managing_orders
Feature: Seeing payment state as paid after checkout steps if order total is zero
    In order to know that the payment is always paid if order total is zero
    As an Administrator
    I want to be able to see payment state as paid when order total was zero

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$10.00"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has promotion "Holiday promotion" with coupon "HOLIDAYPROMO"
        And the promotion gives "$10.00" discount to every order with quantity at least 1
        And the customer logged in

    @api @ui
    Scenario: Seeing payment state as paid on orders list
        Given the customer added product "Angel T-Shirt" to the cart
        And the customer addressed the cart
        And the customer used coupon "HOLIDAYPROMO"
        And the customer chose "Free" shipping method
        And the customer confirmed the order
        And I am logged in as an administrator
        When I browse orders
        Then the last order should have order payment state "Paid"
