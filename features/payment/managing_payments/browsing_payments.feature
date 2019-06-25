@managing_payments
Feature: Browsing payments
    In order to manage all payments regardlessly of orders
    As an Administrator
    I want to browse all payments in the system

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
        And there is a customer "iron@man.com" that placed an order "#00000002"
        And the customer bought a single "Banana"
        And the customer "Tony Stark" addressed it to "Rich street", "90802" "New York" in the "United States" with identical billing address
        And the customer chose "UPS" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @ui
    Scenario: Browsing payments and their states in one channel
        When I browse payments
        Then I should see 2 payments in the list
        And the payments of the "#00000001" order should be "Awaiting payment" for "donald@duck.com"
        And the payments of the "#00000002" order should be "Awaiting payment" for "iron@man.com"
