@managing_payments
Feature: Browsing payments
    In order to manage all payments regardlessly of orders
    As an Administrator
    I want to browse all payments in the system

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "Apple" product
        And I am logged in as an administrator

    @ui
    Scenario: Browsing payments and their states
        When I browse payments
        Then I should see a single payment in the list
        And the payments of the "#00000001" order should be "New" for "amba@fatima.org"

    @ui
    Scenario: Default sorting payments by date
        Given there is an "#00000002" order with "Apple" product
        When I browse payments
        Then I should see payment for "#00000001" order as 1st in the list
        And I should see payment for "#00000002" order as 2st in the list
