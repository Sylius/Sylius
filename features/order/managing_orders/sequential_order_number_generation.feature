@managing_orders
Feature: Sequential order number generation
    In order to have unique order numbers
    As an Administrator
    I want to have order numbers generated sequentially

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Green Arrow"
        And the store ships everywhere for free
        And the store allows paying offline
        And there is a customer "oliver@teamarrow.com" that placed an order
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And there is another customer "barry@teamflash.com" that placed an order
        And the customer bought a single "Green Arrow"
        And the customer chose "Free" shipping method to "United States" with "Offline" payment
        And I am logged in as an administrator

    @ui
    Scenario: Verifying that orders has correct numbers
        When I browse orders
        Then I should see an order with "#00000001" number
        And I should see an order with "#00000002" number
