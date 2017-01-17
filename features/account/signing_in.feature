@customer_login
Feature: Signing in to the store
    In order to view my orders
    As a Visitor
    I want to be able to log in to the store

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "ted@example.com" identified by "bear"

    @ui
    Scenario: Sign in with email and password
        When I want to log in
        And I specify the username as "ted@example.com"
        And I specify the password as "bear"
        And I log in
        Then I should be logged in
