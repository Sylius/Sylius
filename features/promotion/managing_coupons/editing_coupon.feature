@managing_promotion_coupons
Feature: Editing promotion coupon
    In order to change promotion coupon usage limits or expires date
    As an Administrator
    I want to be able to edit coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui
    Scenario: Changing coupon expires date
        Given I want to modify the "SANTA2016" coupon for this promotion
        When I change expires date to "21.05.2019"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should be valid until "21.05.2019"

    @ui
    Scenario: Changing coupons usage limit
        Given I want to modify the "SANTA2016" coupon for this promotion
        When I change its usage limit to 50
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should have 50 usage limit

    @ui
    Scenario: Changing coupons per customer usage limit
        Given I want to modify the "SANTA2016" coupon for this promotion
        When I change its per customer usage limit to 20
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should have 20 per customer usage limit

    @ui
    Scenario: Seeing a disabled code field when editing a coupon
        Given I want to modify the "SANTA2016" coupon for this promotion
        Then the code field should be disabled
