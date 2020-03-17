@managing_administrators
Feature: Administrator unique email and name validation
    In order to uniquely identify administrators
    As an Administrator
    I want to be prevented from adding two administrators with the same email or name

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new administrator with taken email
        Given there is an administrator "ted@example.com"
        When I want to create a new administrator
        And I specify its email as "ted@example.com"
        And I try to add it
        Then I should be notified that email must be unique
        And there should still be only one administrator with an email "ted@example.com"

    @ui @api
    Scenario: Trying to add a new administrator with taken name
        Given there is an administrator with name "Ted"
        When I want to create a new administrator
        And I specify its name as "Ted"
        And I try to add it
        Then I should be notified that name must be unique
        And there should still be only one administrator with name "Ted"
