@managing_promotions
Feature: Editing promotion
    In order to change promotion details
    As an Administrator
    I want to be able to edit a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale" with priority 0
        And there is a promotion "Holiday sale" with priority 1
        And I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field when editing promotion
        When I want to modify a "Christmas sale" promotion
        Then the code field should be disabled

    @ui
    Scenario: Editing promotions usage limit
        Given I want to modify a "Christmas sale" promotion
        When I set its usage limit to 50
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be available to be used only 50 times

    @ui
    Scenario: Editing promotion exclusiveness
        Given I want to modify a "Christmas sale" promotion
        When I make it exclusive
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be exclusive

    @ui
    Scenario: Editing promotions coupon based option
        Given I want to modify a "Christmas sale" promotion
        When I make it coupon based
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be coupon based

    @ui
    Scenario: Editing promotions channels
        Given I want to modify a "Christmas sale" promotion
        When I make it applicable for the "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be applicable for the "United States" channel

    @ui
    Scenario: Editing a promotion with start and end date
        Given I want to modify a "Christmas sale" promotion
        When I make it available from "12.12.2017" to "24.12.2017"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be available from "12.12.2017" to "24.12.2017"

    @ui
    Scenario: Editing promotion after adding a new channel
        Given this promotion gives "$10.00" discount to every order
        When the store also operates on another channel named "EU-WEB"
        Then I should be able to modify a "Christmas sale" promotion

    @ui
    Scenario: Remove priority from existing promotion
        Given I want to modify a "Christmas sale" promotion
        When I remove its priority
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should have priority 1
