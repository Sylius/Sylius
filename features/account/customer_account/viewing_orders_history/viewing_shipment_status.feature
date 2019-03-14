@customer_account
Feature: Viewing details of an order
    In order to check some details of my placed order
    As an Customer
    I want to be able to view details of my placed order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a single "Angel T-Shirt"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @ui
    Scenario: Seeing shipment status before shipping
        When I view the summary of the order "#00000666"
        Then I should see "Ready" as order's shipment status

    @ui
    Scenario: Seeing shipment status after shipping
        Given this order has already been shipped
        When I view the summary of the order "#00000666"
        Then I should see "Shipped" as order's shipment status
