@managing_orders
Feature: Filtering orders by products
    In order to quickly find orders containing specific products
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has a product "Galaxy T-Shirt" with code "cosmic-tee"
        And the store has a product "Space Dress" with code "cosmic-dress"
        And there is an "#0000001" order with "Galaxy T-Shirt" product
        And there is an "#0000002" order with "Space Dress" product
        And there is a customer "lirael@abhorsen.com" that placed an order "#0000003"
        And the customer bought a "Galaxy T-Shirt" and an "Space Dress"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui @api @javascript
    Scenario: Filtering orders by product
        When I browse orders
        And I filter by product "Galaxy T-Shirt"
        Then I should see 2 orders in the list
        And I should see an order with "#0000001" number
        And I should see an order with "#0000003" number

    @ui @api @mink:chromedriver
    Scenario: Filtering orders by multiple products
        When I browse orders
        And I filter by products "Galaxy T-Shirt" and "Space Dress"
        Then I should see 3 orders in the list
        And I should see an order with "#0000001" number
        And I should see an order with "#0000002" number
        And I should see an order with "#0000003" number
