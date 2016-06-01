@managing_orders
Feature: Cancelling orders
    In order to mark order state as cancelled
    As an Administrator
    I want to be able to cancel an order

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer chose "Free" shipping method to "France" with "Cash on Delivery" payment
        And the customer bought a single "PHP T-Shirt"
        And the customer confirmed this order
        And I am logged in as an administrator

    @ui
    Scenario: Cancelling an order
        When I view the summary of the order "#00000022"
        And I cancel this order
        Then I should be notified that it has been successfully updated
        And its state should be "Cancelled"

    @ui
    Scenario: Cannot cancel an order, which is already cancelled
        Given the customer canceled this order
        When I view the summary of the order "#00000022"
        Then I should not be able to cancel this order
