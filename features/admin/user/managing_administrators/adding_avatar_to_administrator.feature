@managing_administrators
Feature: Adding an avatar to an administrator
    In order to visually identify the account
    As an Administrator
    I want to add an avatar to an administrator account

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Adding an avatar to administrator account
        Given I am editing my details
        When I upload the "troll.jpg" image as my avatar
        Then I should see the "troll.jpg" image as my avatar
        And I should see the "troll.jpg" avatar image in the top bar next to my name

    @ui @no-api
    Scenario: Avatar is not added when there is any validation error
        When I want to create a new administrator
        And I upload the "troll.jpg" image as the avatar
        And I specify its email as "Ted"
        And I try to add it
        Then I should be notified that this email is not valid
        And I should not see any image as the avatar
