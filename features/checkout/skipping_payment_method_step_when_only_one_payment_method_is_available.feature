@checkout @javascript
Feature: Skipping payment step when only one payment method is available
    In order to not select payment method if its unnecessary
    As a Customer
    I want to be redirected directly to checkout complete

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And on this channel payment step is skipped if only a single payment method is available
        And the store has a product "Guards! Guards!" priced at "$20.00"
        And the store allows paying with "Paypal Express Checkout"

    @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given I have product "Guards! Guards!" in the cart
        And I have completed addressing step with email "guest@example.com" and "United States" based shipping address
        And I try to complete the shipping step
        Then I should be on the checkout complete step
        And my order's payment method should be "Paypal Express Checkout"

    @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given the store has "Offline" payment method not assigned to any channel
        And I have product "Guards! Guards!" in the cart
        And I have completed addressing step with email "guest@example.com" and "United States" based shipping address
        And I try to complete the shipping step
        Then I should be on the checkout complete step
        And my order's payment method should be "Paypal Express Checkout"

    @ui
    Scenario: Seeing checkout completion page after shipping if only one payment method is available
        Given the store allows paying with "Offline"
        And the payment method "Offline" is disabled
        And I have product "Guards! Guards!" in the cart
        And I have completed addressing step with email "guest@example.com" and "United States" based shipping address
        And I try to complete the shipping step
        Then I should be on the checkout complete step
        And my order's payment method should be "Paypal Express Checkout"
