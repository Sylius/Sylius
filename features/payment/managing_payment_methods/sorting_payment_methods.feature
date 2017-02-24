@managing_payment_methods
Feature: Sorting listed payment methods
    In order to change the order by which payment methods are displayed
    As an Administrator
    I want to sort the payment methods

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a payment method "PayPal Express Checkout" with a code "PAYPAL" and Paypal Express Checkout gateway
        And this payment method is named "Ekspresowy Paypal" in the "Polish (Poland)" locale
        And the store has a payment method "Offline" with a code "offline"
        And this payment method is named "Płatność Offline" in the "Polish (Poland)" locale
        And the store has a payment method "Cash on Delivery" with a code "cash_on_delivery"
        And this payment method is named "Płatność Przy Odbiorze" in the "Polish (Poland)" locale
        And I am logged in as an administrator

    @ui
    Scenario: Payment methods can be sorted by their codes
        Given I am browsing payment methods
        When I start sorting payment methods by code
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have code "cash_on_delivery"

    @ui
    Scenario: Changing the order of sorting payment methods by their codes
        Given I am browsing payment methods
        And the payment methods are already sorted by code
        When I switch the way payment methods are sorted by code
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have code "PAYPAL"

    @ui
    Scenario: Payment methods can be sorted by their names
        Given I am browsing payment methods
        When I start sorting payment methods by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Cash on Delivery"

    @ui
    Scenario: Changing the order of sorting payment methods by their names
        Given I am browsing payment methods
        And the payment methods are already sorted by name
        When I switch the way payment methods are sorted by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "PayPal Express Checkout"

    @ui
    Scenario: Payment methods are always sorted in the default locale
        Given I change my locale to "Polish (Poland)"
        And I am browsing payment methods
        When I start sorting payment methods by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Cash on Delivery"
