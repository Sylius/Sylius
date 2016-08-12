@managing_customers
Feature: Editing an administrator
    In order to change information about an administrator
    As an Administrator
    I want to be able to edit my information

    Background:
        Given the store operates on a single channel in "France"
        And there is an administrator "Gareth Bale" identified by an email "bale@example.com" and a password "bale11"
        And I am logged in as "bale@example.com"

    @todo
    Scenario: Changing password and signing again
        Given I want to change my password
        When I change my password to "abcd"
        And I save my changes
        And I should be notified that it has been successfully edited
        And I log out
        And I log in to the admin panel with email "bale@example.com" and password "abcd"
        Then I should be on the administration dashboard
