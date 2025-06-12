@managing_payment_methods
Feature: Being redirected to previous filtered page
    In order to smoothen navigating through the Admin panel
    As an Administrator
    I want to be redirected to a previously filtered page after taking any action

    Background:
        Given the store operates on a channel named "Poland"
        And the store allows paying with "Cash on Delivery"
        And the store allows paying with "Offline"
        And this payment method has been disabled
        And I am logged in as an administrator

    @no-api @ui @mink:chromedriver
    Scenario: Being redirected to previous filtered page after cancelling editing an existing payment method
        When I browse payment methods
        And I choose enabled filter
        And I filter
        And I want to modify the "Offline" payment method
        And I cancel my changes
        Then I should be redirected to the previous page of only enabled payment methods
