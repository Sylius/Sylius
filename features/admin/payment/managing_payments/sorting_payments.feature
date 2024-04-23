@managing_payments
Feature: Sorting payments
    In order to manage all payments regardless of orders
    As an Administrator
    I want to sort payments by date

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "Apple" product
        And there is an "#00000002" order with "Apple" product ordered later
        And I am logged in as an administrator

    @ui @api-todo
    Scenario: Sorting payments by date in ascending order
        When I browse payments
        And I sort payments by date in ascending order
        Then I should see payment for the "#00000001" order as 1st in the list
        And I should see payment for the "#00000002" order as 2nd in the list

    @ui @api-todo
    Scenario: Sorting payments by date in descending order again
        When I browse payments
        And I sort payments by date in descending order
        Then I should see payment for the "#00000002" order as 1st in the list
        And I should see payment for the "#00000001" order as 2nd in the list
