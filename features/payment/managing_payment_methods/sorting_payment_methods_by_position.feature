@managing_payment_methods
Feature: Sorting listed payment methods by position
    In order to change the order by which payment methods are displayed
    As an Administrator
    I want to sort payment methods by their positions

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows paying with "Paypal Express Checkout" at position 0
        And the store allows paying with "Cash on Delivery" at position 2
        And the store allows paying with "Offline" at position 1
        And I am logged in as an administrator

    @ui
    Scenario: Payment methods are sorted by position in ascending order by default
        When I browse payment methods
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Paypal Express Checkout"
        And the last payment method on the list should have name "Cash on Delivery"

    @ui
    Scenario: Payment method added at no position is added as the last one
        Given the store allows paying with "Credit Card"
        When I browse payment methods
        Then the last payment method on the list should have name "Credit Card"

    @ui
    Scenario: Payment method added at position 0 is added as the first one
        Given the store also allows paying with "Credit Card" at position 0
        When I browse payment methods
        Then the first payment method on the list should have name "Credit Card"
