@customer_account
Feature: Editing a customer profile
    In order to manage my personal information
    As a logged user
    I want to be able to edit my name and email

    Background:
        Given the store operates on a single channel in "France"
        And there is a customer account "Francis Underwood" with email "francis@underwood.com" identified by "whitehouse"
        And I am logged in as "francis@underwood.com"

    @todo
    Scenario: Changing my first name and last name
        Given I want to modify my customer profile
        And I specify the first name as "Will"
        And I specify the last name as "Conway"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer name should be "Will Conway"

    @todo
    Scenario: Changing my email
        Given I want to modify my customer profile
        And I specify the email as "frank@underwood.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer email should be "frank@underwood.com"
