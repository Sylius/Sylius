@stale_live_component
Feature: Stale cart LiveComponent cannot mutate a completed order
    In order to protect completed orders from data corruption
    As a Developer
    I want the cart LiveComponent to be safe against stale browser state

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Sylius T-Shirt" priced at "$19.99"

    @ui @javascript
    Scenario: Clearing cart from a stale page does not delete a completed order
        Given I added product "Sylius T-Shirt" to the cart
        And I check the details of my cart
        And I note the current order id for later assertions
        When the order is completed in the background
        And I clear my cart from the stale page without reloading
        Then the completed order should still exist in the database
        And the order checkout state should be "completed"

    @ui @javascript
    Scenario: Removing an item from a stale page does not mutate a completed order
        Given I added product "Sylius T-Shirt" to the cart
        And I check the details of my cart
        And I note the current order id for later assertions
        When the order is completed in the background
        And I remove first item from cart from the stale page without reloading
        Then the completed order should still exist in the database
        And the order should still have 1 item

    @ui @javascript
    Scenario: Increasing item quantity on a stale page does not mutate a completed order
        Given I added product "Sylius T-Shirt" to the cart
        And I check the details of my cart
        And I note the current order id for later assertions
        When the order is completed in the background
        And I increase the quantity of the first cart item to 2 on the stale page without reloading
        Then the completed order should still exist in the database
        And the order item quantity should still be 1
