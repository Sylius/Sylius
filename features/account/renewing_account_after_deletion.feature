@customer_registration
Feature: Registering an account again after it has been deleted
    In order to set up a new account after I deleted it from the system
    As a Visitor
    I want to be able to register again with the same e-mail

    Background:
        Given the store operates on a single channel in "United States"
        And there was account of "ted@example.com" with password "pswd"
        But its account was deleted

    @ui
    Scenario: Registering again after my account deletion
        When I want to again register a new account
        And I specify the first name as "Ted"
        And I specify the last name as "Shovel"
        And I specify the email as "ted@example.com"
        And I specify the password as "ted1"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
