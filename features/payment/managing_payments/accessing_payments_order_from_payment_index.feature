@managing_payments
Feature: Accessing payment's order from the payment index
    In order to make payment and orders management more fluent
    As an Administrator
    I want to be able to access order's page from payments index

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "UPS" shipping method with "$10.00" fee
        And the store has a product "Apple"
        And the store allows paying with "Cash on Delivery"
        And there is a new "#00000001" order with "Apple" product
        And I am logged in as an administrator

    @ui
    Scenario: Accessing payment's order from the payments index
        When I browse payments
        And I move to the details of first payment's order
        Then I should see order page with details of order "00000001"
