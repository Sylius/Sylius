@managing_payments
Feature: Browsing payments
    In order to manage all payments regardlessly of orders
    As an Administrator
    I want to browse all payments in the system

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "Apple" product
        And I am logged in as an administrator

    @ui @api
    Scenario: Browsing payments and their states
        When I browse payments
        Then I should see a single payment in the list
        And the payment of the "#00000001" order should be "New" for "amba@fatima.org"

    @ui @api
    Scenario: Not seeing payments in cart state
        Given the customer "customer@example.com" added "Apple" product to the cart
        When I browse payments
        Then I should see a single payment in the list

    @ui @api
    Scenario: Payments are sorted by newest as default
        Given there is an "#00000002" order with "Apple" product ordered later
        When I browse payments
        Then I should see payment for the "#00000002" order as 1st in the list
        And I should see payment for the "#00000001" order as 2nd in the list
