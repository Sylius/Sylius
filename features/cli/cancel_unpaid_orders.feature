@canceling_unpaid_orders @cli
Feature: Canceling unpaid orders
    In order to have my orders list free from completed but unpaid orders
    As a Developer
    I want to have unpaid orders cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$10.00" in "United States" channel
        And the store ships everywhere for free
        And the store allows paying with "Paypal Express Checkout"

    Scenario: Having order cancelled after 6 days of being unpaid
        Given there is a customer "john.doe@gmail.com" that has placed 2 orders with numbers "#00000025" and "#00000026"
        And the customer has refunded the order with number "#00000026"
        When the order with number "#00000025" has not been paid for 6 days
        Then only unpaid order with number "#00000025" should be canceled
        And I should be informed that unpaid order have been cancelled
