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

    @ui
    Scenario: Being able to see Cancel button when an order is unpaid and unshipped
        When I browse my orders
        Then I should see Cancel button next to the order "#00000666"
