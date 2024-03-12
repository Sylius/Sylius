@managing_administrators
Feature: Changing an administrator's avatar
    In order to always have an actual photo
    As an Administrator
    I want to be able to update an avatar of an existing administrator

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Changing an avatar of an administrator
        Given I have the "ford.jpg" image as my avatar
        And I am editing my details
        When I update the "troll.jpg" image as my avatar
        Then I should see the "troll.jpg" image as my avatar
