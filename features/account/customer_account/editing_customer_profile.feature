@customer_account
Feature: Editing a customer profile
    In order to manage my personal information
    As a Customer
    I want to be able to edit my name and email

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
        And I am logged in as "francis@underwood.com"

    @ui
    Scenario: Changing my first name and last name
        When I want to modify my profile
        And I specify the first name as "Will"
        And I specify the last name as "Conway"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And my name should be "Will Conway"

    @ui
    Scenario: Changing my email
        When I want to modify my profile
        And I specify the customer email as "frank@underwood.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And my email should be "frank@underwood.com"

    @ui
    Scenario: Changing the default address
        When I want to modify my profile
        And I set the customer address name to "Frank" "Underwood"
        And I set the customer address phone number to "00000"
        And I set the customer address company to "Sylius Inc"
        And I set the customer address country to "United States"
        And I set the customer address to "Somestreet" "Some City" "Some Postcode" "Province"
