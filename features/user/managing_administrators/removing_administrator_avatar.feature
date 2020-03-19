@managing_administrators
Feature: Removing an administrator avatar
    In order to properly identify the account
    As an Administrator
    I want to remove an administrator avatar of an account

    Background:
        Given I am logged in as an administrator
        And this administrator has the "troll.jpg" image as avatar

    @ui @api
    Scenario: Removing an administrator avatar
        Given I am editing my details
        When I remove the avatar
        Then I should not see the "troll.jpg" avatar image in the top bar next to my name
        And I should not see the "troll.jpg" avatar image in the additional information section of my account
