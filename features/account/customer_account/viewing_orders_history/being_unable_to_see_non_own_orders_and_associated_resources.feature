@customer_account
Feature: Being unable to see non-own orders and associated resources
    In order to customers follow only their orders
    As a Store Owner
    I want not to be able to see non-own orders and associated resources by customer

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "PHP T-Shirt" product
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And there is a customer "john@example.com" that placed order with "PHP T-Shirt" product to "United States" based billing address with "Free" shipping method and "Cash on Delivery" payment method

    @api
    Scenario: Being unable to see non-own order
        When I try to see the order placed by a customer "john@example.com"
        Then I should not be able to see that order

    @api
    Scenario: Being unable to see non-own order item
        When I try to see one of the items from the order placed by a customer "john@example.com"
        Then I should not be able to see that item

    @api
    Scenario: Being unable to see non-own order item unit
        When I try to see one of the units from the order placed by a customer "john@example.com"
        Then I should not be able to see that unit

    @api
    Scenario: Being unable to see non-own shipment
        When I try to see the shipment of the order placed by a customer "john@example.com"
        Then I should not be able to see that shipment

    @api
    Scenario: Being unable to see non-own payment
        When I try to see the payment of the order placed by a customer "john@example.com"
        Then I should not be able to see that payment
