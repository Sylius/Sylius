@managing_orders
Feature: Order gets fulfilled after it's been paid and shipped
    In order to know which orders don't need further monitoring
    As an Administrator
    I want orders which have been paid and shipped to be fulfilled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Green Arrow"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "oliver@teamarrow.com" that placed an order
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator
        And I am viewing the summary of this order

    @ui
    Scenario: An order can be fulfilled
        When I mark this order as paid
        And I ship this order
        Then its state should be "Fulfilled"

    @ui
    Scenario: An order which has been shipped gets fulfilled after paying
        Given this order has already been shipped
        When I mark this order as paid
        Then its state should be "Fulfilled"

    @ui
    Scenario: An order which has been paid for gets fulfilled after shipping it
        Given this order is already paid
        When I ship this order
        Then its state should be "Fulfilled"

    @ui
    Scenario: A paid, but not shipped order is not fulfilled
        When I mark this order as paid
        Then its state should be "New"

    @ui
    Scenario: A shipped, but not paid order is not fulfilled
        When I ship this order
        Then its state should be "New"

    @ui
    Scenario: Fulfilled orders cannot be cancelled
        When I mark this order as paid
        And I ship this order
        Then I should not be able to cancel this order
