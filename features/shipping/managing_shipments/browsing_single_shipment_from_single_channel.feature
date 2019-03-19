@managing_shipments
Feature: Browsing shipments
    In order to manage all shipments regardlessly of orders
    As an Administrator
    I want to browse all shipments in the system


    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And this order has already been shipped
        And I am logged in as an administrator

    @ui
    Scenario: Browsing shipments and their states
        When I browse shipments
        Then I should see single shipment in the list
        And the shipment of the "#00000001" order should be "shipped"
