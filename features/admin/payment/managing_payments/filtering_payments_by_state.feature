@managing_payments
Feature: Filtering payments by state
    In order to browse all payments with a chosen state
    As an Administrator
    I want to filter payments by state

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a "New" "#00000001" order with "Apple" product
        And there is a "Completed" "#00000002" order with "Apple" product
        And there is a "Processing" "#00000003" order with "Apple" product
        And there is a "Refunded" "#00000004" order with "Apple" product
        And there is a "Cancelled" "#00000005" order with "Apple" product
        And there is a "Failed" "#00000006" order with "Apple" product
        And I am logged in as an administrator

    @ui @api
    Scenario: Filtering payments in state "New"
        When I browse payments
        And I choose "new" as a payment state
        And I filter
        Then I should see "3" payments in the list
        And I should see the payment of the "#00000001" order
        And I should see also the payment of the "#00000005" order
        And I should see also the payment of the "#00000006" order
        But I should not see the payment of the "#00000002" order

    @ui @api
    Scenario: Filtering payments in state "Completed"
        When I browse payments
        And I choose "completed" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000002" order
        But I should not see the payment of the "#00000003" order

    @ui @api
    Scenario: Filtering payments in state "Processing"
        When I browse payments
        And I choose "processing" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000003" order
        But I should not see the payment of the "#00000004" order

    @ui @api
    Scenario: Filtering payments in state "Refunded"
        When I browse payments
        And I choose "refunded" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000004" order
        But I should not see the payment of the "#00000005" order

    @ui @api
    Scenario: Filtering payments in state "Cancelled"
        When I browse payments
        And I choose "cancelled" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000005" order
        But I should not see the payment of the "#00000006" order

    @ui @api
    Scenario: Filtering payments in state "Failed"
        When I browse payments
        And I choose "failed" as a payment state
        And I filter
        Then I should see a single payment in the list
        And I should see the payment of the "#00000006" order
        But I should not see the payment of the "#00000001" order
