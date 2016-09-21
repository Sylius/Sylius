@managing_payment_methods
Feature: Sorting payment methods on list
    In order to change the order by which payment methods are displayed
    As an Administrator
    I want to sort the payment methods

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a payment method "Paypal Express Checkout" with a code "express_checkout"
        And this payment method is named "Ekspresowy Paypal" in "Polish (Poland)"
        And the store has a payment method "Offline" with a code "paying_offline"
        And this payment method is named "Płatność Offline" in "Polish (Poland)"
        And the store has a payment method "Cash on Delivery" with a code "personal_payment"
        And this payment method is named "Pięniądze przy Odbiorze albo Życie" in "Polish (Poland)"
        And I am logged in as an administrator

    @ui
    Scenario: Payment methods are sorted by code in ascending order by default
        When I browse payment methods
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have code "express_checkout"

    @ui
    Scenario: Changing the order of sorting by code
        Given I am browsing payment methods
        When I switch the way payment methods are sorted by code
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have code "personal_payment"

    @ui
    Scenario: Payment methods can be sorted by their names
        Given I am browsing payment methods
        When I start sorting payment methods by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Paypal Express Checkout"

    @ui
    Scenario: Changing the order of sorting payment methods by their names
        Given I am browsing payment methods
        And the payment methods are already sorted by name
        When I switch the way payment methods are sorted by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Cash on Delivery"

    @ui
    Scenario: Payment methods are always sorted in the default locale
        Given I change my locale to "Polish (Poland)"
        And I am browsing payment methods
        When I start sorting payment methods by name
        Then I should see 3 payment methods in the list
        And the first payment method on the list should have name "Paypal Express Checkout"
