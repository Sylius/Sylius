@managing_payments
Feature: Filtering payment requests
    In order to see specific payment requests
    As an Administrator
    I want to be able to filter payment requests on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store allows paying with "Credit Card"
        And there is an "#00000001" order with "PHP T-Shirt" product
        And there is an "#00000002" order with "PHP T-Shirt" product
        And the payment request action "authorize" has been executed for order "#00000001" with the payment method "Credit Card"
        And the payment request action "authorize" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And the payment request action "capture" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And the payment request action "sync" has been executed for order "#00000001" with the payment method "Cash on Delivery"
        And the payment request action "capture" has been executed for order "#00000002" with the payment method "Cash on Delivery"
        And I am logged in as an administrator

    @api @ui
    Scenario: Filtering payment requests by action
        When I browse payment requests of an order "#00000001"
        And I filter by the "capture" action
        Then there should be 1 payment request on the list
        And it should be the payment request with action "capture"

    @api @ui @mink:chromedriver
    Scenario: Filtering payment requests by payment method
        When I browse payment requests of an order "#00000001"
        And I filter by the "Credit Card" payment method
        Then there should be 1 payment request on the list
        And it should be the payment request with payment method "Credit Card"

    @api @ui
    Scenario: Filtering payment requests by state "New"
        When I browse payment requests of an order "#00000001"
        And I filter by the "new" state
        Then there should be 4 payment requests on the list

    @api @ui
    Scenario: Filtering payment requests by state "Completed"
        When I browse payment requests of an order "#00000001"
        And I filter by the "completed" state
        Then there should be 0 payment request on the list
