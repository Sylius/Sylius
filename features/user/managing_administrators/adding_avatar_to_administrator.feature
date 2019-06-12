@managing_administrators
Feature: Adding an avatar to an administrator
    In order to visually identify the account
    As an Administrator
    I want to add an avatar to an administrator account

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding an avatar to administrator account
        Given I am editing my details
        When I upload the "troll.jpg" image as my avatar
        Then I should see the "troll.jpg" image as my avatar
        And I should see the "troll.jpg" avatar image in the top bar next to my name
