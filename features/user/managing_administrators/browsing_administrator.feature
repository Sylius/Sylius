@managing_administrators
Feature: Browsing administrators
    In order to see all administrators in the store
    As an Administrator
    I want to browse administrators

    Background:
        Given there is an administrator "mr.banana@example.com"
        And there is also an administrator "ted@example.com"
        And I am logged in as "ted@example.com" administrator

    @ui
    Scenario: Browsing administrators in store
        When I want to browse administrators
        Then there should be 2 administrators in the list
        And I should see the administrator "mr.banana@example.com" in the list
