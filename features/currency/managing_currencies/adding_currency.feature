@managing_currencies
Feature: Adding a new currency
    In order to sell products in different currencies
    As an Administrator
    I want to add a new currency to the store

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new currency
        Given I want to add a new currency
        When I choose "Euro"
        And I add it
        Then I should be notified that it has been successfully created
        And the currency "Euro" should appear in the store
