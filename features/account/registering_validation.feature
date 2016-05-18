@customer_account
Feature: Account registration
    In order to avoid making mistakes when registering account
    As a visitor
    I need to be able to create an account in the store

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Trying to register a new account with email that has been already used
        Given there is user "goodman@gmail.com" identified by "heisenberg"
        And I want to register a new account
        When I specify email with "goodman@gmail.com"
        And I try to register this account
        Then I should be notified that email is already used
        And I should not be logged in

    @ui
    Scenario: Trying to register a new account without specifying first name
        Given I want to register a new account
        When I do not specify first name
        And I specify last name with "Goodman"
        And I specify email with "goodman@gmail.com"
        And I specify password with "heisenberg"
        And I try to register this account
        Then I should be notified that first name is required
        And I should not be logged in

    @ui
    Scenario: Trying to register a new account without specifying last name
        Given I want to register a new account
        When I do not specify last name
        And I specify first name with "Saul"
        And I specify email with "goodman@gmail.com"
        And I specify password with "heisenberg"
        And I confirm this password
        And I try to register this account
        Then I should be notified that last name is required
        And I should not be logged in

    @ui
    Scenario: Trying to register a new account without specifying password
        Given I want to register a new account
        When I do not specify password
        And I specify first name with "Saul"
        And I specify last name with "Goodman"
        And I specify email with "goodman@gmail.com"
        And I try to register this account
        Then I should be notified that password is required
        And I should not be logged in

    @ui
    Scenario: Trying to register a new account without confirming password
        Given I want to register a new account
        When I specify first name with "Saul"
        Then I specify last name with "Goodman"
        And I specify email with "goodman@gmail.com"
        And I specify password with "heisenberg"
        And I do not confirm password
        And I try to register this account
        Then I should be notified that password do not match
        And I should not be logged in

    @ui
    Scenario: Trying to register a new account without specifying email
        Given I want to register a new account
        When I do not specify email
        And I specify first name with "Saul"
        And I specify last name with "Goodman"
        And I try to register this account
        Then I should be notified that email is required
        And I should not be logged in
