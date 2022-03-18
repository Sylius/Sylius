@managing_shipments
Feature: Filtering shipments by a channel
    In order to browse only relevant shipments
    As an Administrator
    I want to be able to filter shipments from a specific channel on the list

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
    Scenario: Filtering shipments by channel on index
        When I browse shipments
        And I choose "United States" as a channel filter
        And I filter
        Then I should see a single shipment in the list
        And I should see a shipment of order "#00000001"
        But I should not see a shipment of order "#00000003"
