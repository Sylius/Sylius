@managing_orders
Feature: Checking payment state of a cancelled order
    In order to check payment state after cancelling an order
    As an Administrator
    I want to payment has proper state

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "France" with "Cash on Delivery" payment
        And the customer confirmed this order
        And I am logged in as an administrator

    @todo
    Scenario: Checking payment state of a placed order
        When I view the summary of the order "#00000022"
        And I cancel this order
        Then I should be notified that it has been successfully updated
        Then it should have cancelled payment state
