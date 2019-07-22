@managing_administrators
Feature: Remove an administrator avatar
    In order to properly identify the account
    As an Administrator
    I want to remove an administrator avatar of an account

    Background:
        Given I am logged in as an administrator
        And this administrator has the "troll.jpg" image as avatar

    @ui
    Scenario: Remove an administrator avatar
        Given I am editing my details
        When I remove the avatar image
        And I should not see the avatar image in the top bar next to my name
