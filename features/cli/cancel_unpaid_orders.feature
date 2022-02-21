@canceling_unpaid_orders
Feature: Canceling unpaid orders
    In order to have my orders list free from completed but unpaid orders
    As a Developer
    I want to have unpaid orders cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00" in "United States" channel
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "john.doe@gmail.com" that placed an order "#00000025"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment

    @cli
    Scenario: Having order cancelled after 6 days of being unpaid
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000026"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000026" is already paid
        But the customer has refunded the order with number "#00000026"
        And the order "#00000025" has not been paid for 6 days
        When I run cancel unpaid orders command
        Then only the order with number "#00000025" should be canceled
        And I should be informed that unpaid orders have been canceled
