@managing_shipments
Feature: Shipping a shipment from shipment list
    In order to manage all shipments status
    As an Administrator
    I want to be able to ship a shipment from shipments list

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
    Scenario: Shipping a shipment from shipments index
        When I browse shipments
        And I ship the shipment of order "#00000001"
        Then I should be notified that the shipment has been successfully shipped
        And I should see the shipment of order "#00000001" as "Shipped"
