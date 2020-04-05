@checkout
Feature: Having registration form prefilled after checkout
    In order to make future purchases with ease
    As an Visitor
    I want to have account registration form prefilled after placing an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Having prefilled registration form after checkout
        Given I have product "PHP T-Shirt" in the cart
        And I complete addressing step with email "john@example.com" and "United States" based billing address
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        Then I should see the thank you page
        And I should be able to proceed to the registration
        And the registration form should be prefilled with "john@example.com" email

    @ui
    Scenario: Not being able to create an account if customer is logged in
        Given I am a logged in customer
        And I have product "PHP T-Shirt" in the cart
        And I complete addressing step with "United States" based billing address
        And I proceed with "Free" shipping method and "Offline" payment
        And I confirm my order
        Then I should see the thank you page
        And I should not be able to proceed to the registration
