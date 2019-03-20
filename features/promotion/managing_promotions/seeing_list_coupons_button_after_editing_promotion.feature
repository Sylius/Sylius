@managing_promotions
Feature: Seeing list coupons button after editing promotions
    In order to change promotion details
    As an Administrator
    I want to be able to edit a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale" with priority 0
        And there is a promotion "Holiday sale" with priority 1
        And I am logged in as an administrator

    @ui
    Scenario: Editing promotion exclusiveness and seeing "List coupons"
        Given I want to modify a "Christmas sale" promotion
        When I make it exclusive
        And I save my changes
        And I should see "List coupons"
