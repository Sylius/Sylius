@managing_string_blocks
Feature: Static contents validation
    In order to avoid making mistakes when managing a string block
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new string block without specifying its body
        Given I want to add a new string block
        When I set its name to "return-info"
        And I add it
        Then I should be notified that body is required
        And the string block "return-info" should not be added

    @ui
    Scenario: Trying to add a new string block without specifying its name
        Given I want to add a new string block
        When I set its body to "Free shipping for orders over 10$!"
        And I add it
        Then I should be notified that name is required
