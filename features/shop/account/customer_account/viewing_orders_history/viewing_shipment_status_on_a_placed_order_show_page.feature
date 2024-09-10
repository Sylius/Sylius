@customer_account
Feature: Viewing shipment state on a placed order show page
    In order to be aware whether my order has already been shipped
    As a Customer
    I want to be able to view details of my shipment state

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Angel T-Shirt" priced at "$39.00"
        And the store has a product "Angel Mug" priced at "$19.00"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And I am a logged in customer
        And I placed an order "#00000666"
        And I bought a "Angel T-Shirt" and a "Angel Mug"
        And I addressed it to "Lucifer Morningstar", "Seaside Fwy", "90802" "Los Angeles" in the "United States"
        And for the billing address of "Mazikeen Lilim" in the "Pacific Coast Hwy", "90806" "Los Angeles", "United States"
        And I chose "Free" shipping method with "Cash on Delivery" payment

    @api @ui
    Scenario: Seeing shipment state when it has not been shipped yet
        When I view the summary of my order "#00000666"
        Then the shipment state should be "Ready"

    @api @ui
    Scenario: Seeing shipment state after shipping
        Given this order has already been shipped
        When I view the summary of my order "#00000666"
        Then the shipment state should be "Shipped"

    @api @ui
    Scenario: Seeing order's shipment state when it has not been shipped yet
        When I view the summary of my order "#00000666"
        Then the order's shipment state should be "Ready"

    @api @ui
    Scenario: Seeing order's shipment state after shipping
        Given this order has already been shipped
        When I view the summary of my order "#00000666"
        Then the order's shipment state should be "Shipped"
