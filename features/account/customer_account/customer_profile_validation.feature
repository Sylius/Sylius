@customer_account
Feature: Customer profile validation
    In order to avoid making mistakes when changing my personal information
    As a Customer
    I want to be prevented from entering incorrect values

    Background:
        Given the store operates on a single channel in "France"
        And the store has customer "claire@underwood.com"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
        And I am logged in as "francis@underwood.com"

    @ui
    Scenario: Trying to remove my first name
        Given I want to modify my profile
        When I remove the first name
        And I try to save my changes
        Then I should be notified that the first name is required
        And my name should still be "Francis Underwood"

    @ui
    Scenario: Trying to remove my last name
        Given I want to modify my profile
        When I remove the last name
        And I try to save my changes
        Then I should be notified that the last name is required
        And my name should still be "Francis Underwood"

    @ui
    Scenario: Trying to remove my email
        Given I want to modify my profile
        When I remove the email
        And I try to save my changes
        Then I should be notified that the email is required
        And my email should still be "francis@underwood.com"

    @ui
    Scenario: Trying to change my email to an existing value
        Given I want to modify my profile
        When I specify the email as "claire@underwood.com"
        And I try to save my changes
        Then I should be notified that the email is already used
        And my email should still be "francis@underwood.com"

    @ui
    Scenario: Trying to change my email to an invalid value
        Given I want to modify my profile
        When I specify the email as "francisunderwood"
        And I try to save my changes
        Then I should be notified that the email is invalid
        And my email should still be "francis@underwood.com"
