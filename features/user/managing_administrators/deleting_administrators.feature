@managing_administrators
Feature: Deleting an administrator
    In order to get rid of deprecated administrators
    As an Administrator
    I want to be able to delete other administrator account

    Background:
        Given there is an administrator "mr.banana@example.com"
        And there is also an administrator "ted@example.com"
        And I am logged in as "ted@example.com" administrator

    @ui
    Scenario: Deleting an administrator
        Given I want to browse administrators
        When I delete administrator with email "mr.banana@example.com"
        Then I should be notified that it has been successfully deleted
        And there should not be "mr.banana@example.com" administrator anymore

    @ui
    Scenario: The administrator account of currently logged in user cannot be deleted
        Given I want to browse administrators
        When I delete administrator with email "ted@example.com"
        Then I should be notified that it cannot be deleted
        And there should still be only one administrator with an email "ted@example.com"
