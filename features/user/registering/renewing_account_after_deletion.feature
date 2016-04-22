@registering
Feature: Registering an account again after it has been deleted
    In order to set up a new account after I deleted it from the system
    As a Visitor
    I want to be able to register again with the same e-mail

    Background:
        Given the store operates on a single channel in "France"
        And there was account of "ted@example.com" with password "pswd"
        But his account was deleted

    @ui
    Scenario: Registering again after my account deletion
        When I try to register again with email "ted@example.com"
        Then I should be successfully registered
