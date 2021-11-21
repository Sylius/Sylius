@administrator_login
Feature: Blocking login for disabled administrators
    In order to avoid login of unwanted admins
    As an Admin
    I want to be unable to login with disabled account

    Background:
        Given the store operates on a single channel in "United States"
        And there is an administrator "admin@example.com" identified by "sylius"
        And this administrator account is disabled

    @ui @api
    Scenario: Sign in with email and password
        Given I want to log in
        When I specify the username as "admin@example.com"
        And I specify the password as "sylius"
        And I log in
        Then I should not be logged in
