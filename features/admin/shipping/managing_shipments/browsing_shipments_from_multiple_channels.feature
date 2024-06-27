@managing_shipments
Feature: Browsing shipments from multiple channels
    In order to manage all shipments regardlessly of orders
    As an Administrator
    I want to browse all shipments in the system

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001" in channel "United States"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And this order has already been shipped
        And the store has country "Canada"
        And the store operates on another channel named "Canada" in "CAD" currency
        And the store has a zone "Canada" with code "CA"
        And this zone has the "Canada" country member
        And the store has "FEDEX" shipping method with "$10.00" fee
        And the store allows paying with "Bank transfer"
        And the store has a product "Orange"
        And there is a customer "iron@man.com" that placed an order "#00000003" in channel "Canada"
        And the customer bought a single "Orange"
        And the customer "Tony Stark" addressed it to "Rich street", "90802" "New York" in the "Canada" with identical billing address
        And the customer chose "FEDEX" shipping method with "Bank transfer" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing shipments and their states from multiple channels
        When I browse shipments
        And I should see 2 shipments in the list
        Then the shipment of the "#00000001" order should be "Shipped" for "donald@duck.com" in "United States" channel
        And the shipment of the "#00000003" order should be "Ready" for "iron@man.com" in "Canada" channel
