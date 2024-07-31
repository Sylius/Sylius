@managing_orders
Feature: Being unable to finalize order's payment when at least one item has become tracked after the purchase
    In order to mark order's payment state as complete when there is a sufficient reserved stock
    As an Administrator
    I want to be able to finalize payment only when there is a sufficient reserved stock

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "PHP T-Shirt"
        And there is a customer "john@example.com" that placed an order "#00000001"
        And the customer bought 5 "PHP T-Shirt" products
        And the customer "John Doe" addressed it to "Seaside Fwy", "90802" "Los Angeles" in the "United States" with identical billing address
        And the customer chose "Free" shipping method with "Cash on Delivery" payment
        And I am logged in as an administrator

    @api @ui
    Scenario: Being unable to finalize order's payment when one item has become tracked after the purchase
        Given I am viewing the summary of the order "#00000001"
        And the "PHP T-Shirt" product's inventory has become tracked with 2 items
        When I mark this order as paid
        Then I should be notified that the order's payment could not be finalized due to insufficient stock
        And it should have payment state "New"

    @api @ui
    Scenario: Being unable to finalize order's payment when one item has become tracked after the purchase
        Given I am viewing the summary of the order "#00000001"
        And the "PHP T-Shirt" product's inventory has become tracked with 6 items
        When I mark this order as paid
        Then I should be notified that the order's payment could not be finalized due to insufficient stock
        And it should have payment state "New"
