@order
Feature: Deleting an order
    In order to get rid of test and bad orders
    As an Administrator
    I want to be able to delete an order with all it's details from the registry

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the customer "john.doe@gmail.com" placed an order "#00000022"
        And the customer chose "Free" shipping method to "France" with "Cash on Delivery" payment
        And the customer bought single "PHP T-Shirt"

    @domain
    Scenario: Deleted order should disappear from the registry
        When I delete the order "#00000022"
        Then this order should not exist in the registry

    @todo
    Scenario: Payments of a deleted order should disappear from the registry
        When I delete the order "#00000022"
        Then there should be no "Cash on Delivery" payments in the registry

    @todo
    Scenario: Shipments of a deleted order should disappear from the registry
        When I delete the order "#00000022"
        Then there should be no shipments with "Free" shipping method in the registry

    @todo
    Scenario: Order items are deleted together with an order
        When I delete the order "#00000022"
        Then the order item with product "PHP T-Shirt" should not exist

    @todo
    Scenario: Order adjustments are deleted together with an order
        When I delete the order "#00000022"
        Then adjustments of this order should not exist

    @todo
    Scenario: Billing and shipping addresses are deleted with an order
        When I delete the order "#00000022"
        Then billing and shipping addresses of this order should not exist
