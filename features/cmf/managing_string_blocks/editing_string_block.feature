@managing_string_blocks
Feature: Editing a string block
    In order to change string block
    As an Administrator
    I want to be able to edit a string block

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Change title of a string block
        Given the store has string block "delivery-info" with body "Delivery only to the US!"
        And I want to edit this string block
        When I change its body to "We deliver everywhere!"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this string block should have body "We deliver everywhere!"
