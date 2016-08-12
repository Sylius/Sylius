@managing_administrators
Feature: Adding a new administrator
    In order to create new administrator account
    As an Administrator
    I want to add a administrator to the store

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new administrator
        Given I want to create a new administrator
        When I specify its email as "l.skywalker@gmail.com"
        And I specify its password as "lightsaber"
        And I add it
        Then I should be notified that it has been successfully created
        And the administrator "l.skywalker@gmail.com" should appear in the store

    @ui
    Scenario: Adding a new administrator with full details
        Given I want to create a new administrator
        When I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its password as "lightsaber"
        And I add it
        Then I should be notified that it has been successfully created
        And the administrator "l.skywalker@gmail.com" should appear in the store
