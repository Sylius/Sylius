@managing_administrators
Feature: Administrator validation
    In order to avoid making mistakes when managing administrator timezone
    As an Administrator
    I want to be prevented from adding or editing it without valid timezone

    Background:
        Given I am logged in as an administrator

    @api
    Scenario: Trying to add a new administrator without valid timezone
        When I want to create a new administrator
        And I specify its email as "l.skywalker@gmail.com"
        And I specify its name as "Luke"
        And I specify its password as "lightsaber"
        And I specify its timezone as "not valid timezone value"
        And I try to add it
        Then I should be notified that the timezone is not valid

    @api
    Scenario: Trying to edit administrator without valid timezone
        Given I am editing my details
        When I specify its timezone as "not valid timezone value"
        And I try to save my changes
        Then I should be notified that the timezone is not valid
