@customer_account
Feature: Cancelling unpaid and unshipped order
    In order to resign from buying merchandise I don't want to buy
    As a Customer
    I want to be able to cancel my order when it is unpaid and unshipped

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Cancelling unpaid and unshipped order
        When I browse my orders
        And I cancel the order "#00000666"
        Then the order "#00000666" should be cancelled

    @ui
    Scenario: Being unable to cancel paid but unshipped order
        Given the order "#00000666" is already paid
        When I browse my orders
        Then it should not be possible to cancel the order "#00000666"

    @ui
    Scenario: Being unable to cancel shipped but unpaid order
        Given the order "#00000666" is already shipped
        When I browse my orders
        Then it should not be possible to cancel the order "#00000666"

    @ui
    Scenario: Being unable to cancel paid and shipped order
        Given the order "#00000666" is already paid
        And this order has already been shipped
        When I browse my orders
        Then it should not be possible to cancel the order "#00000666"

    @ui
    Scenario: Being unable to cancel an order when it has already been cancelled
        Given the order "#00000666" was cancelled
        When I browse my orders
        Then it should not be possible to cancel the order "#00000666"
