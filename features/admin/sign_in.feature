@administrator_login
Feature: Sign in to the store
    In order to view my orders
    As a Visitor
    I want to be able to log in to the store

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "ted@example.com" identified by "bear"
        And there is an administrator "admin@example.com" identified by "sylius"

    @ui
    Scenario: Sign in with email and password
        Given I want to log in
        When I specify the username as "admin@example.com"
        And I specify the password as "sylius"
        And I log in
        Then I should be logged in

    @ui
    Scenario: Sign in with bad credentials
        Given I want to log in
        When I specify the username as "admin@example.com"
        And I specify the password as "pswd"
        And I log in
        Then I should be notified about bad credentials
        And I should not be logged in

    @ui
    Scenario: Sign in using customer account
        Given I want to log in
        When I specify the username as "bear@example.com"
        And I specify the password as "bear"
        And I log in
        Then I should be notified about bad credentials
        And I should not be logged in
