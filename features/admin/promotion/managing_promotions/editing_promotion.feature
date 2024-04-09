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

    @api @ui
    Scenario: Being unable to change code of promotion
        When I want to modify a "Christmas sale" promotion
        Then I should not be able to edit its code

    @api @ui
    Scenario: Editing promotions usage limit
        When I want to modify a "Christmas sale" promotion
        And I set its usage limit to 50
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be available to be used only 50 times

    @api @ui
    Scenario: Editing promotion exclusiveness
        When I want to modify a "Christmas sale" promotion
        And I set it as exclusive
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be exclusive

    @api @ui
    Scenario: Editing promotions coupon based option
        When I want to modify a "Christmas sale" promotion
        And I make it coupon based
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be coupon based

    @api @ui
    Scenario: Editing promotions channels
        When I want to modify a "Christmas sale" promotion
        And I make it applicable for the "United States" channel
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be applicable for the "United States" channel

    @api @ui
    Scenario: Editing a promotion with start and end date
        When I want to modify a "Christmas sale" promotion
        And I make it available from "12.12.2017" to "24.12.2017"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should be available from "12.12.2017" to "24.12.2017"

    @api @ui
    Scenario: Editing promotion after adding a new channel
        Given this promotion gives "$10.00" discount to every order
        When the store also operates on another channel named "EU-WEB"
        Then I should be able to modify a "Christmas sale" promotion

    @ui @no-api
    Scenario: Remove priority from existing promotion
        When I want to modify a "Christmas sale" promotion
        And I remove its priority
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should have priority 1

    @api @ui
    Scenario: Setting promotion to the lowest priority
        When I want to modify a "Christmas sale" promotion
        And I set its priority to "-1"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the "Christmas sale" promotion should have priority 1
