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
        And there is a customer "donald@duck.com" that placed an order "#00000001" in channel "United States"
        And the customer bought a single "Apple"
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @api @email
    Scenario: Shipping a shipment from shipments index
        When I browse shipments
        And I ship the shipment of order "#00000001"
        Then I should be notified that the shipment has been successfully shipped
        And an email with the "UPS" shipment's confirmation for the "#00000001" order should be sent to "donald@duck.com"
        And I should see the shipment of order "#00000001" as "Shipped"

    @ui @api @email
    Scenario: Shipping a shipment with tracking code from shipments index
        When I browse shipments
        And I ship the shipment of order "#00000001" with "AWDDXS-SAAQQ-SEFFX-CCDSE" tracking code
        Then I should be notified that the shipment has been successfully shipped
        And an email with the shipment's confirmation of the order "#00000001" should be sent to "donald@duck.com"

    @ui @api
    Scenario: Setting date when a shipment has been shipped
        Given it is "20-02-2020 10:30:05" now
        When I browse shipments
        And I ship the shipment of order "#00000001"
        Then I should see the shipment of order "#00000001" as "Shipped"
        And I should see the shipment of order "#00000001" shipped at "20-02-2020 10:30:05"

    @api
    Scenario: Shipping a shipment that has been already shipped
        Given this order has already been shipped
        When I try to ship the shipment of order "#00000001"
        Then I should be notified that shipment has been already shipped
