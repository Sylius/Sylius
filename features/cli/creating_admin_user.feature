@creating_admin_user_via_cli
Feature: Creating an administrator via CLI
    In order to create an administrator account from the command line
    As a developer
    I want to be able to create an administrator with chosen access levels via CLI

    @cli
    Scenario: Creating an administrator with administration access only
        When I want to create a new administrator using the command
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its first name as "Luke"
        And I specify its last name as "Skywalker"
        And I specify its password as "lightsaber"
        And I select administration access
        And I run the command
        Then I should be informed that the admin user has been created
        And the "l.skywalker@gmail.com" administrator should have administration access
        But the "l.skywalker@gmail.com" administrator should not have API access

    @cli
    Scenario: Creating an administrator with API access only
        When I want to create a new administrator using the command
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its first name as "Luke"
        And I specify its last name as "Skywalker"
        And I specify its password as "lightsaber"
        And I select API access
        And I run the command
        Then I should be informed that the admin user has been created
        And the "l.skywalker@gmail.com" administrator should have API access
        But the "l.skywalker@gmail.com" administrator should not have administration access

    @cli
    Scenario: Creating an administrator with both access levels
        When I want to create a new administrator using the command
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its first name as "Luke"
        And I specify its last name as "Skywalker"
        And I specify its password as "lightsaber"
        And I select both access levels
        And I run the command
        Then I should be informed that the admin user has been created
        And the "l.skywalker@gmail.com" administrator should have administration access
        And the "l.skywalker@gmail.com" administrator should have API access
