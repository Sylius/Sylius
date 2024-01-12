@managing_shipments
Feature: Accessing shipment's order from the shipments index
    In order to make shipments and orders management more fluent
    As an Administrator
    I want to be able to access order's page from shipments index

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001" in channel "United States"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Accessing shipment's order from the shipment
        When I browse shipments
        And I move to the details of first shipment's order
        Then I should see the details of order "#00000001"
