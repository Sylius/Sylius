@managing_orders
Feature: Filtering orders by a shipping method
    In order to filter orders by a specific shipping method
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Blue ElePHPant"
        And the store has a product "White ElePHPant"
        And the store has a product "Red ElePHPant"
        And the store ships everywhere for Free
        But the store has "DHL" shipping method with "$10.00" fee
        And the store allows paying Offline
        And there is a customer "jack@teambiz.com" that placed an order "#000001337"
        And the customer bought a single "Blue ElePHPant"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is a customer "gui@teambiz.com" that placed an order "#000000042"
        And the customer bought a single "White ElePHPant"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is another customer "max@teambiz.com" that placed an order "#000001338"
        And the customer bought a single "Red ElePHPant"
        And the customer chose "DHL" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering orders by DHL shipping method
        When I browse orders
        And I choose "DHL" as a shipping method filter
        And I filter
        Then I should see a single order in the list
        And I should see an order with "#000001338" number
        And I should not see an order with "#000001337" number

    @ui @api
    Scenario: Filtering orders by an another shipping method
        When I browse orders
        And I choose "Free" as a shipping method filter
        And I filter
        Then I should see 2 orders in the list
        And I should see an order with "#000001337" number
        And I should see an order with "#000000042" number
        But I should not see an order with "#000001338" number
