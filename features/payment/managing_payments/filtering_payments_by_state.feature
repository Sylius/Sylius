@managing_payments
Feature: Filtering payments by state
    In order to filter payments by state
    As an Administrator
    I want to browse all payments with chosen state

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a new "#00000001" order with "Apple" product
        And there is a new "#00000002" order with "Apple" product
        And I am logged in as an administrator

    @ui
    Scenario: Filtering payments by a chosen state
        When I browse payments
        And I complete the payment of order "#00000002"
        And I choose "New" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000001" order
        But I should not see a payment of order "#00000002"
