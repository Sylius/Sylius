@managing_administrators
Feature: Deleting an administrator
    In order to delete administrator account
    As an Administrator
    I want to be able to delete the administrator

    Background:
        Given there is an administrator "mr.banana@example.com"
        And there is also an administrator "ted@example.com"
        And I am logged in as "ted@example.com" administrator

    @ui
    Scenario: Deleting an administrator
        Given I want to browse administrators
        When I delete administrator with email "mr.banana@example.com"
        Then I should be notified that it has been successfully deleted
        And there should be 1 administrators in the list
        And there should be no "mr.banana@example.com" administrator anymore

    @ui
    Scenario: The administrator account of currently logged in user cannot be deleted
        Given I want to browse administrators
        When I delete administrator with email "ted@example.com"
        Then I should be notified that it cannot be deleted
        And there should be 2 administrators in the list
        And there should still be only one administrator with email "ted@example.com"