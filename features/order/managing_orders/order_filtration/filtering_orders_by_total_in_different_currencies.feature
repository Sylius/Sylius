@managing_orders
Feature: Filtering orders by total in different currencies
    In order to filter orders with total in a specific range and currency
    As an Administrator
    I want to be able to filter orders by their total and currency

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on a channel named "Web-UK" in "GBP" currency
        And the store has "Apple T-Shirt", "Pineapple T-Shirt" and "Pen T-Shirt" products
        And the store has customer "John Hancock" with email "hancock@superheronope.com"
        And this customer has placed an order "#00000001" buying a single "Apple T-Shirt" product for "$100" on the "Web-US" channel
        And this customer has also placed an order "#00000002" buying a single "Pineapple T-Shirt" product for "$200" on the "Web-US" channel
        And this customer has also placed an order "#00000003" buying a single "Pen T-Shirt" product for "$150.50" on the "Web-US" channel
        And this customer has also placed an order "#00000004" buying a single "Apple T-Shirt" product for "£200" on the "Web-UK" channel
        And this customer has also placed an order "#00000005" buying a single "Pineapple T-Shirt" product for "£150.50" on the "Web-UK" channel
        And this customer has also placed an order "#00000006" buying a single "Pen T-Shirt" product for "£100" on the "Web-UK" channel
        And I am logged in as an administrator
        And I am browsing orders

    @ui
    Scenario: Filtering orders by currency alone
        When I choose "British Pound" as the filter currency
        And I filter
        Then I should see 3 orders in the list
        But I should not see any orders with currency "USD"

    @ui
    Scenario: Filtering orders with total greater than specified amount
        When I choose "US Dollar" as the filter currency
        And I specify filter total being greater than 100
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000002" number
        And I should see an order with "#00000003" number
        But I should not see an order with "#00000001" number
        And I should not see any orders with currency "GBP"

    @ui
    Scenario: Filtering orders with total less than specified amount
        When I choose "US Dollar" as the filter currency
        And I specify filter total being less than 200
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000001" number
        And I should see an order with "#00000003" number
        But I should not see an order with "#00000002" number
        And I should not see any orders with currency "GBP"

    @ui
    Scenario: Filtering order with total from a specified range
        When I choose "British Pound" as the filter currency
        And I specify filter total being greater than 150.50
        And I specify filter total being less than 250
        And I filter
        Then I should see a single order in the list
        And I should see an order with "#00000004" number
        But I should not see an order with "#00000005" number
        And I should not see an order with "#00000006" number
        And I should not see any orders with currency "USD"

    @ui
    Scenario: Filtering orders by total in given range but with no currency provided
        When I specify filter total being greater than 150
        And I specify filter total being less than 200
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#00000003" number
        And I should see an order with "#00000005" number
