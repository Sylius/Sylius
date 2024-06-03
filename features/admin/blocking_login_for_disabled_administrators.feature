@administrator_security
Feature: Blocking login for disabled administrators
    In order to avoid login of unwanted admins
    As an Admin
    I want to be unable to login with disabled account

    Background:
        Given the store operates on a single channel in "United States"
        And there is an administrator "admin@example.com" identified by "sylius"

    @ui @api
    Scenario: Sign in with email and password
        Given this administrator account is disabled
        And I want to log in
        When I specify the username as "admin@example.com"
        And I specify the password as "sylius"
        And I log in
        Then I should not be logged in

    @ui @api
    Scenario: Revoking the access while administrator is logged in
        Given I am logged in as "admin@example.com" administrator
        When this administrator account becomes disabled
        And I try to browse administrators
        Then I should not be logged in
