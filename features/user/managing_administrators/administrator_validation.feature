@managing_administrators
Feature: Administrator validation
    In order to avoid making mistakes when managing administrators
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new administrator without email and name
        Given I want to create a new administrator
        When I do not specify its email
        And I do not specify its name
        And I try to add it
        Then I should be notified that the email is required
        And I should be notified that the name is required
        And this administrator should not be added

    @ui
    Scenario: Trying to add a new administrator without password
        Given I want to create a new administrator
        When I do not specify its password
        And I try to add it
        Then I should be notified that the password is required
        And this administrator should not be added

    @ui
    Scenario: Trying to add a new administrator with invalid email
        Given I want to create a new administrator
        When I specify its email as "Ted"
        And I try to add it
        Then I should be notified that this email is not valid
        And this administrator should not be added
