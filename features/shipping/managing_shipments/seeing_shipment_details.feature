@managing_shipments
Feature: Seeing basic information about shipment
    In order to see details of a specific shipment
    As an Administrator
    I want to be able to view shipment show page

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Apple" and "Banana" products
        And the store has "UPS" shipping method with "$10.00" fee
        And the store allows paying with "Cash on Delivery"
        And there is a customer "donald@duck.com" that placed an order "#00000001"
        And the customer bought 2 "Apple" products
        And the customer bought 3 "Banana" products
        And the customer "Donald Duck" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Seeing basic information about shipment
        When I view the first shipment of the order "#00000001"
        Then I should see 2 "Apple" units in the list
        And I should see 3 "Banana" units in the list
