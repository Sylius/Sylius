@customer_account
Feature: Customer profile validation
    In order to avoid making mistakes when changing my personal information
    As a logged user
    I want to be prevented from changing fields to incorrect values

    Background:
        Given the store operates on a single channel in "France"
        And the store has customer "claire@underwood.com"
        And there is a customer account "Francis Underwood" with email "francis@underwood.com" identified by "whitehouse"
        And I am logged in as "francis@underwood.com"

    @todo
    Scenario: Trying to remove my first name
        Given I want to modify my customer profile
        And I remove the first name
        And I try to save my changes
        Then I should be notified that the first name is required
        And the customer name should still be "Francis Underwood"

    @todo
    Scenario: Trying to remove my last name
        Given I want to modify my customer profile
        And I remove the last name
        And I try to save my changes
        Then I should be notified that the first name is required
        And the customer name should still be "Francis Underwood"

    @todo
    Scenario: Trying to remove my email
        Given I want to modify my customer profile
        And I remove the email
        And I try to save my changes
        Then I should be notified that the first name is required
        And the customer email should still be "francis@underwood.com"

    @todo
    Scenario: Trying to change my email to an existing value
        Given I want to edit my customer profile
        And I specify the email as "claire@underwood.com"
        And I try to save my changes
        Then I should be notified that the email is already used
        And the customer email should still be "francis@underwood.com"
