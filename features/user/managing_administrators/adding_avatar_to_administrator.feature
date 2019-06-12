@managing_administrators
Feature: Adding an avatar to administrator
    In order to add new avatar to the administrator account
    As an Administrator
    I want to add new avatar to the administrator account

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding an avatar to administrator account
        When I want to edit this administrator
        And I attach the "troll.jpg" avatar
        And I save my changes
        Then I should see this administrator account with avatar "troll.jpg"
        And I should see "troll.jpg" avatar in main bar where this administrator is logged
