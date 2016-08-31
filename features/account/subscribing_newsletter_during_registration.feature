@customer_registration
Feature: Subscribing to the newsletter during registration
    In order to be up-to-date with products and promotions
    As a Visitor
    I need to be able to create an account with possibility to subscribe to the newsletter

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Subscribing to the newsletter during registration
        Given I want to register a new account
        When I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I subscribe to the newsletter
        And I register this account
        Then I should be notified that new account has been successfully created
        And I should be subscribed to the newsletter
