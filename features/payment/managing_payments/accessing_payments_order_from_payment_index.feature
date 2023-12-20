@managing_payments
Feature: Accessing payment's order from the payment index
    In order to make payment and orders management more fluent
    As an Administrator
    I want to be able to access order's page from payments index

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is an "#00000001" order with "Apple" product
        And I am logged in as an administrator

    @ui @api
    Scenario: Accessing payment's order from the payment
        Given I am browsing payments
        When I go to the details of the first payment's order
        Then I should see the details of order "#00000001"
