@managing_orders
Feature: Filtering orders by a customer
    In order to quickly find orders placed by a specific customer
    As an Administrator
    I want to be able to filter orders on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has customer "Bob Ross" with email "ross@bob.com"
        And this customer has placed an order "#00000001" at "2016-12-04 08:00"
        And the store has customer "Lirael Abhorsen" with email "lirael@abhorsen.com"
        And this customer has also placed an order "#00000002" at "2016-12-05 09:00"
        And the store has customer "Ghastly Bespoke" with email "ghastly@suits.com"
        And this customer has also placed an order "#00000003" at "2016-12-06 10:00"
        And I am logged in as an administrator

    @api-todo @ui @mink:chromedriver
    Scenario: Filtering orders by a customer
        When I browse orders
        And I filter by customer "Lirael Abhorsen"
        Then I should see a single order in the list
        And I should see an order with "#00000002" number

    @api-todo @ui @mink:chromedriver
    Scenario: Filtering orders by another customer
        When I browse orders
        And I filter by customer "Ghastly"
        Then I should see a single order in the list
        And I should see an order with "#00000003" number
