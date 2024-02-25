@managing_shipments
Feature: Browsing shipments
    In order to manage all shipments regardlessly of orders
    As an Administrator
    I want to browse all shipments in the system

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store has a product "Banana"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And this order has already been shipped
        And there is a customer "iron@man.com" that placed an order "#00000002" later
        And the customer bought a single "Banana"
        And the customer "Tony Stark" addressed it to "Rich street", "90802" "New York" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing shipments and their states in one channel
        When I browse shipments
        Then I should see 2 shipments in the list
        And I should see the shipment of order "#00000001" as "Shipped"
        And I should see the shipment of order "#00000002" as "Ready"

    @ui @api
    Scenario: Shipments are sorted by newest as default
        When I browse shipments
        Then I should see shipment for the "#00000002" order as 1st in the list
        And I should see shipment for the "#00000001" order as 2nd in the list

    @ui @api
    Scenario: Not seeing shipments in cart state
        Given the customer "customer@example.com" added "Banana" product to the cart
        When I browse shipments
        Then I should see only 2 shipments in the list
