@managing_string_blocks
Feature: Adding a new string block
    In order to manage content
    As an Administrator
    I want to add string block to my site

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding string block
        Given I want to add a new string block
        When I set its name to "free-shipping-info"
        And I set its body to "Free shipping for orders over 40$"
        And I add it
        Then I should be notified that it has been successfully created
        And the string block "free-shipping-info" should appear in the store
