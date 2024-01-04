@managing_shipments
Feature: Filtering shipments by state
    In order to filter shipments by state
    As an Administrator
    I want to browse all shipments with a chosen state

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
        And there is a customer "iron@man.com" that placed an order "#00000003" in channel "United States"
        And the customer bought a single "Apple"
        And the customer "Tony Stark" addressed it to "Rich street", "90802" "New York" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering payments in state "Shipped"
        When I browse shipments
        And I choose "shipped" as a shipment state
        And I filter
        Then I should see a single shipment in the list
        And I should see a shipment of order "#00000001"
        But I should not see a shipment of order "#00000003"
