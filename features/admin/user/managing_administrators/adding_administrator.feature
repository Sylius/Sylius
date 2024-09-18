@managing_administrators
Feature: Adding a new administrator
    In order to create new administrator account
    As an Administrator
    I want to add a administrator to the store

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new administrator
        When I want to create a new administrator
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its password as "lightsaber"
        And I specify its locale as "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the administrator "l.skywalker@gmail.com" should appear in the store

    @ui @api
    Scenario: Adding a new administrator and log in with its credentials
        When I want to create a new administrator
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its password as "lightsaber"
        And I specify its locale as "English (United States)"
        And I enable it
        And I add it
        Then I should be able to log in as "l.skywalker@gmail.com" authenticated by "lightsaber" password
