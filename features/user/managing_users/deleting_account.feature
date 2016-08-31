@managing_users
Feature: Deleting the customer account
    In order to delete customer account on demand
    As an Administrator
    I want to be able to remove customer account details from the system

    Background:
        Given there is a user "theodore@example.com" identified by "pswd"
        And I am logged in as an administrator

    @ui
    Scenario: Deleting account should not delete customer details
        When I delete the account of "theodore@example.com" user
        Then the user account should be deleted
        But the customer with this email should still exist

    @ui
    Scenario: A customer with no user cannot be deleted
        Given the account of "theodore@example.com" was deleted
        Then I should not be able to delete it again
