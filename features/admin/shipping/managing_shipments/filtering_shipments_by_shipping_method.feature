@managing_shipments
Feature: Filtering shipments by a shipping method
    In order to find only shipments from a specific shipping method
    As an Administrator
    I want to be able to filter shipments on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Audi A6 allroad quattro"
        And the store ships everywhere for Free
        But the store has "DHL" shipping method with "$10.00" fee
        And the store allows paying Offline
        And there is a customer "jack@teambiz.com" that placed an order "#000001337"
        And the customer bought a single "Audi A6 allroad quattro"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is a customer "gui@teambiz.com" that placed an order "#000000042"
        And the customer bought a single "Audi A6 allroad quattro"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is another customer "max@teambiz.com" that placed an order "#000001338"
        And the customer bought a single "Audi A6 allroad quattro"
        And the customer chose "DHL" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering shipments by a shipping method
        When I browse shipments
        And I choose "DHL" as a shipping method filter
        And I filter
        Then I should see a single shipment in the list
        And I should see a shipment of order "#000001338"
        But I should not see a shipment of order "#000001337"
        And I should not see a shipment of order "#000000042"
