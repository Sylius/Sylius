@canceling_unpaid_orders
Feature: Canceling unpaid orders
    In order to have my orders list free from completed but unpaid orders
    As a Developer
    I want to have unpaid orders cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00" in "United States" channel
        And the store ships everywhere for Free
        And the store allows paying Offline
        And there is a customer "john.doe@gmail.com" that placed an order "#00000025"
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000025" has not been paid for 6 days
        And there is a customer "john.doe@gmail.com" that placed an order "#00000026"

    @cli
    Scenario: The order with refunded state has not been canceled
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000026" is already paid
        But the customer has refunded the order with number "#00000026"
        When I run cancel unpaid orders command
        Then only the order with number "#00000025" should be canceled
        And I should be informed that unpaid orders have been canceled

    @cli
    Scenario: The order with paid state has not been canceled
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And the order "#00000026" is already paid
        When I run cancel unpaid orders command
        Then only the order with number "#00000025" should be canceled
        And I should be informed that unpaid orders have been canceled

    @cli
    Scenario: The order with awaiting payment state has not been canceled
        And the customer bought a single "PHP T-Shirt"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        When I run cancel unpaid orders command
        Then only the order with number "#00000025" should be canceled
        And I should be informed that unpaid orders have been canceled

    @cli
    Scenario: The order with cart state has not been canceled
        When I run cancel unpaid orders command
        Then only the order with number "#00000025" should be canceled
        And I should be informed that unpaid orders have been canceled
