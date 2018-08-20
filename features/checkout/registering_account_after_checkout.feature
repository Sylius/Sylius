@checkout
Feature: Registering a new account after checkout
    In order to make future purchases with ease
    As an Visitor
    I want to be able to create an account in the store after placing an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store ships everywhere for free
        And the store allows paying offline

    @ui
    Scenario: Registering a new account after checkout
        Given I have product "PHP T-Shirt" in the cart
        And I have completed addressing step with email "john@example.com" and "United States" based shipping address
        And I have proceeded order with "Free" shipping method and "Offline" payment
        And I have confirmed order
        When I proceed to the registration
        And I specify a password as "sylius"
        And I confirm this password
        And I register this account
        And I verify my account using link sent to "john@example.com"
        Then I should be able to log in as "john@example.com" with "sylius" password
