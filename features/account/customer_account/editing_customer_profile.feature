@customer_account
Feature: Editing a customer profile
    In order to manage my personal information
    As a Customer
    I want to be able to edit my name and email

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "francis@underwood.com" identified by "sylius"
        And I am logged in as "francis@underwood.com"

    @ui @api
    Scenario: Changing my first name and last name
        When I want to modify my profile
        And I specify the first name as "Will"
        And I specify the last name as "Conway"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And my name should be "Will Conway"

    @ui @email @no-api
    Scenario: Changing my email if channel requires verification
        When I want to modify my profile
        And I specify the customer email as "frank@underwood.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And I should be notified that the verification email has been sent
        And it should be sent to "frank@underwood.com"
        And I should not be logged in

    @api @no-ui
    Scenario: Changing my email if channel requires verification
        When I want to modify my profile
        And I specify the customer email as "frank@underwood.com"
        And I save my changes
        And I should not be logged in

    @ui @no-api
    Scenario: Changing my email if channel does not require verification
        Given "United States" channel has account verification disabled
        When I want to modify my profile
        And I specify the customer email as "frank@underwood.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And my account should not be verified
        And my email should be "frank@underwood.com"

    @api @no-ui
    Scenario: Changing my email if channel does not require verification
        Given "United States" channel has account verification disabled
        When I want to modify my profile
        And I specify the customer email as "frank@underwood.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And my email should be "frank@underwood.com"
