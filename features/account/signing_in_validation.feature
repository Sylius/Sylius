@customer_login
Feature: Signing in to the store validation
    In order to avoid making mistakes when signing in to the store
    As a Visitor
    I want to be prevented from signing in with bad credentials

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "ted@example.com" identified by "bear"

    @ui
    Scenario: Trying to sign in with bad credentials
        When I want to log in
        And I specify the username as "bear@example.com"
        And I specify the password as "pswd"
        And I try to log in
        Then I should be notified about bad credentials
        And I should not be logged in

    @ui
    Scenario: Trying to sign in without confirming account
        When I register with email "sylius@example.com" and password "sylius"
        And I want to log in
        And I specify the username as "sylius@example.com"
        And I specify the password as "sylius"
        And I try to log in
        Then I should be notified about disabled account
        And I should not be logged in

    @ui
    Scenario: Trying to sign in after my account was deleted
        Given my account "ted@example.com" was deleted
        When I want to log in
        And I specify the username as "ted@example.com"
        And I specify the password as "pswd"
        And I try to log in
        Then I should be notified about bad credentials
        And I should not be logged in
