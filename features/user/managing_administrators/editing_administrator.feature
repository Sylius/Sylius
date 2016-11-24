@managing_administrators
Feature: Editing an administrator
    In order to change information about an administrator
    As an Administrator
    I want to be able to edit the administrator

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Changing name and email of an existing administrator
        Given there is an administrator "ted@example.com" identified by "bear"
        And I want to edit this administrator
        When I change its name as "Jon Snow"
        And I change its email as "jon@example.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this administrator with name "Jon Snow" should appear in the store

    @ui
    Scenario: Changing password of an existing administrator and trying sign in with old password
        Given there is an administrator "ted@example.com" identified by "bear"
        And I want to edit this administrator
        When I change its name as "bear123"
        And I change its password as "example"
        And I save my changes
        Then I should not be able to log in as "ted@example.com" authenticated by "bear" password

    @ui
    Scenario: Changing password of an existing administrator and sign in again
        Given there is an administrator "ted@example.com" identified by "bear"
        And I want to edit this administrator
        When I change its name as "bear123"
        And I change its password as "example"
        And I save my changes
        Then I should be able to log in as "ted@example.com" authenticated by "example" password
