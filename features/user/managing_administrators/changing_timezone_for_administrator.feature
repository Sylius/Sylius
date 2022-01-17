@managing_administrators
Feature: Changing a timezone for administrator
    In order to browse content in my timezone
    As an Administrator
    I want to be able to change my timezone

    Background:
        Given I am logged in as an administrator

    @api @ui
    Scenario: Changing a timezone of administrator
        Given I am editing my details
        When I choose "Europe/Warsaw" as my timezone
        And I save my changes
        Then I should have "Europe/Warsaw" as my timezone
