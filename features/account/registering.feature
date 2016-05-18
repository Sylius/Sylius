@customer_account
Feature: Account registration
    In order to order products
    As a visitor
    I need to be able to create an account in the store

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Registering a new account with minimum information
        Given I want to register a new account
        When I specify first name with "Saul"
        And I specify last name with "Goodman"
        And I specify email with "goodman@gmail.com"
        And I specify password with "heisenberg"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
        And I should be logged in

    @ui
    Scenario: Registering a new account with all details
        Given I want to register a new account
        When I specify first name with "Saul"
        And I specify last name with "Goodman"
        And I specify email with "goodman@gmail.com"
        And I specify password with "heisenberg"
        And I confirm this password
        And I specify phone number with "123456789"
        And I register this account
        Then I should be notified that new account has been successfully created
        And I should be logged in
