@customer_account
Feature: Customer cancelling unpaid and unshipped order
    In order to mark order state as cancelled
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
    Scenario: Being able to see Cancel button when an order is unpaid and unshipped
        When I browse my orders
        Then I should see Cancel button next to the order "#00000666"

    @ui
    Scenario: Cancelling an order when it's unpaid and unshipped
        Given I browse my orders
        When I click Cancel button next to the order "#00000666"
        Then the order "#00000666" should be cancelled

    @ui
    Scenario: Being unable to cancel an order when it's paid and unshipped
        Given the order "#00000666" is already paid
        When I browse my orders
        Then the Cancel button next to the order "#00000666" should not be visible

    @ui
    Scenario: Being unable to cancel an order when it's unpaid and shipped
        Given the order "#00000666" is already shipped
        When I browse my orders
        Then the Cancel button next to the order "#00000666" should not be visible

    @ui
    Scenario: Being unable to cancel an order when it's paid and shipped
        Given the order "#00000666" is already paid
        And this order has already been shipped
        When I browse my orders
        Then the Cancel button next to the order "#00000666" should not be visible

    @ui
    Scenario: Being unable to cancel an order when it has already been cancelled
        Given the order "#00000666" was cancelled
        When I browse my orders
        Then the Cancel button next to the order "#00000666" should not be visible
