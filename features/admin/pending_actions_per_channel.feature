@admin_dashboard
Feature: Pending actions for a specific channel
    In order to keep track of required actions in a specific channel
    As an Administrator
    I want to see the list of my pending actions

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "PHP Mug"
        And the store allows paying with "Cash on Delivery"
        And there is a customer "john@example.com" that placed an order "#00000001" in channel "United States"
        And the customer bought a single "PHP Mug"
        And the customer "John Doe" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And there is a customer "john@example.com" that placed an order "#00000002" in channel "United States"
        And the customer bought a single "PHP Mug"
        And the customer "John Doe" addressed it to "Elm street", "90802" "Duckburg" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And the store has country "Canada"
        And the store operates on another channel named "Canada" in "CAD" currency
        And the store has a zone "Canada" with code "CA"
        And this zone has the "Canada" country member
        And the store has "FEDEX" shipping method with "$10.00" fee
        And the store allows paying with "Bank transfer"
        And the store has a product "Orange"
        And there is a customer "jane@example.com" that placed an order "#00000003" in channel "Canada"
        And the customer bought a single "Orange"
        And the customer "Jane Doe" addressed it to "Rich street", "90802" "New York" in the "Canada" with identical billing address
        And the customer chose "FEDEX" shipping method with "Bank transfer" payment
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Seeing channel related pending actions
        When I open administration dashboard
        Then I should see 2 orders to process in the pending actions
        And I should see 2 shipments to ship in the pending actions
        And I should see 2 pending payments in the pending actions

    @no-api @ui @mink:chromedriver
    Scenario: Seeing channel related pending actions in different channel
        When I open administration dashboard
        And I choose "Canada" channel
        Then I should see 1 order to process in the pending actions
        And I should see 1 shipment to ship in the pending actions
        And I should see 1 pending payment in the pending actions

    @no-api @ui
    Scenario: Seeing product reviews to approve in pending actions
        Given this product also has accepted reviews rated 5, 4 and 1
        And this product also has review rated 3 which is not accepted yet
        When I open administration dashboard
        Then I should see 1 product review to approve in the pending actions

    @no-api @ui
    Scenario: Seeing product variants out of stock in pending actions
        Given the store has a product "Symfony T-Shirt"
        And the product "Symfony T-Shirt" is out of stock
        When I open administration dashboard
        Then I should see 1 product variant out of stock in the pending actions
