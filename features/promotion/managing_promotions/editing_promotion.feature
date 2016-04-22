@managing_promotions
Feature: Editing promotion
    In order to change promotion details
    As an Administrator
    I want to be able to edit a promotion

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field when editing promotion
        Given I want to modify a "Christmas sale" promotion
        Then the code field should be disabled

    @ui
    Scenario: Editing promotions usage limit
        Given I want to modify a "Christmas sale" promotion
        And I set its usage limit to 50
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be available to be used only 50 times

    @ui
    Scenario: Editing promotion exclusiveness
        Given I want to modify a "Christmas sale" promotion
        And I make it exclusive
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be exclusive

    @ui
    Scenario: Editing promotions coupon based option
        Given I want to modify a "Christmas sale" promotion
        And I make it coupon based
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be coupon based

    @ui
    Scenario: Editing promotions channels
        Given I want to modify a "Christmas sale" promotion
        And I make it applicable for the "France" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be applicable for the "France" channel
