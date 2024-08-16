@managing_payments
Feature: Completing a payment from its list
    In order to easily manage all payments statuses
    As an Administrator
    I want to be able to complete a payment from payments list

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "Apple" product
        And I am logged in as an administrator

    @ui @api
    Scenario: Completing a payment from payments index
        When I browse payments
        And I complete the payment of order "#00000001"
        Then I should be notified that the payment has been completed
        And I should see the payment of order "#00000001" as "Completed"
