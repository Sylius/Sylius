@checkout
Feature: Skipping payment step when only one payment method is available
    In order to not select payment method if its unnecessary
    As a Visitor
    I want to be redirected directly to checkout complete

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for Free
        And on this channel payment step is skipped if only a single payment method is available
        And the store has a product "Guards! Guards!" priced at "$20.00"
        And the store allows paying with "Bank transfer"

    @api @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given I added product "Guards! Guards!" to the cart
        When I complete addressing step with email "guest@example.com" and "United States" based billing address
        And I complete the shipping step with the first shipping method
        Then I should be on the checkout complete step
        And my order's payment method should be "Bank transfer"

    @api @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given the store has "Offline" payment method not assigned to any channel
        And I added product "Guards! Guards!" to the cart
        When I complete addressing step with email "guest@example.com" and "United States" based billing address
        And I complete the shipping step with the first shipping method
        Then I should be on the checkout complete step
        And my order's payment method should be "Bank transfer"

    @api @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given the store allows paying with "Offline"
        And the payment method "Offline" is disabled
        And I added product "Guards! Guards!" to the cart
        When I complete addressing step with email "guest@example.com" and "United States" based billing address
        And I complete the shipping step with the first shipping method
        Then I should be on the checkout complete step
        And my order's payment method should be "Bank transfer"

    @api @ui
    Scenario: Preventing skipping the payment method choosing step when no payment method is available
        Given the store has disabled all payment methods
        And I added product "Guards! Guards!" to the cart
        When I complete addressing step with email "guest@example.com" and "United States" based billing address
        And I complete the shipping step with the first shipping method
        Then I should be on the checkout payment step
