@managing_administrators
Feature: Changing administrator avatar
    In order to change avatar of administrator
    As an Administrator
    I want to be able to changing avatar of an existing administrator

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Changing a single avatar of an administrator
        Given this administrator has an avatar "ford.jpg"
        When I want to edit this administrator
        And I update the "troll.jpg" avatar
        And I save my changes
        Then I should see this administrator account with avatar "troll.jpg"
