@managing_orders
Feature: Cancelling orders
    In order to mark order state as cancelled
    As an Administrator
    I want to be able to cancel an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Cancelling an order
        When I view the summary of the order "#00000022"
        And I cancel this order
        Then I should be notified that it has been successfully updated
        And its state should be "Cancelled"
        And it should have shipment in state "Cancelled"
        And it should have payment state "Cancelled"
        And there should be only 1 payment

    @ui
    Scenario: Cannot cancel an order, which is already cancelled
        Given the customer cancelled this order
        When I view the summary of the order "#00000022"
        Then I should not be able to cancel this order

    @ui
    Scenario: Checking order payment state of a cancelled order
        Given this order was cancelled
        When I browse orders
        Then this order should have order payment state "Cancelled"
