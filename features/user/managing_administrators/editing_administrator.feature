@managing_administrators
Feature: Editing an administrator
    In order to change information about an administrator
    As an Administrator
    I want to be able to edit the administrator

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Changing name and email of an existing administrator
        Given there is an administrator "ted@example.com" identified by "bear"
        When I want to edit this administrator
        And I specify its name as "Jon Snow"
        And I specify its email as "jon@example.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this administrator with name "Jon Snow" should appear in the store

    @ui
    Scenario: Changing password of an existing administrator
        Given there is an administrator "ted@example.com" identified by "bear"
        When I want to edit this administrator
        And I specify its password as "example"
        And I save my changes
        Then I should be notified that it has been successfully edited

    @ui
    Scenario: Changing password of an existing administrator and trying sign in with old password
        Given there is an administrator "ted@example.com" identified by "bear"
        When I want to edit this administrator
        And I specify its password as "example"
        And I save my changes
        And I log out
        And I try to log in with email "ted@example.com" and password "bear"
        Then I should be notified about bad credentials

    @ui
    Scenario: Changing password of an existing administrator and sign in again
        Given there is an administrator "ted@example.com" identified by "bear"
        When I want to edit this administrator
        And I specify its password as "example"
        And I save my changes
        And I log out
        And I try to log in with email "ted@example.com" and password "example"
        Then I should be on the administration dashboard
