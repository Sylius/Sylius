@managing_shipments
Feature: Setting date when a shipment has been shipped
    In order to manage shipping date of all shipments
    As an Administrator
    I want to be able to see shipping date on shipments list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001" in channel "united states"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Setting date when a shipment has been shipped
        Given this order has already been shipped
        When I browse shipments
        Then I should see the shipment of order "#00000001" as "Shipped"
        And I should see the shipment of order "#00000001" with a shipped date
