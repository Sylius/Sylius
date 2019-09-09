@managing_payments
Feature: Filtering payments by channel
    In order to browse only relevant payments
    As an Administrator
    I want to be able to filter payments from a specific channel on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has a product "Apple"
        And there is an "#00000001" order with "Apple" product in this channel
        And the store operates on another channel named "Canada" in "CAD" currency
        And the store has country "Canada"
        And the store has a product "Orange"
        And the store allows paying with "Bank transfer"
        And there is an "#00000002" order with "Orange" product in this channel
        And I am logged in as an administrator

    @ui
    Scenario: Filtering payments by channel on index
        When I browse payments
        And I choose "Canada" as a channel filter
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000002" order
        But I should not see a payment of order "#00000001"
