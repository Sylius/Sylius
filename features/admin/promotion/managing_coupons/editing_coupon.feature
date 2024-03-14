@managing_promotion_coupons
Feature: Editing promotion coupon
    In order to change promotion coupon usage limits or expires date
    As an Administrator
    I want to be able to edit coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui @api
    Scenario: Changing coupon expires date
        When I want to modify the "SANTA2016" coupon for this promotion
        And I change its expiration date to "21.05.2019"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should be valid until "21.05.2019"

    @ui @api
    Scenario: Changing coupons usage limit
        When I want to modify the "SANTA2016" coupon for this promotion
        And I change its usage limit to 50
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should have 50 usage limit

    @ui @api
    Scenario: Changing coupons per customer usage limit
        When I want to modify the "SANTA2016" coupon for this promotion
        And I change its per customer usage limit to 20
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should have 20 per customer usage limit

    @ui @api
    Scenario: Changing whether it can be reused from cancelled orders
        When I want to modify the "SANTA2016" coupon for this promotion
        And I make it not reusable from cancelled orders
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this coupon should not be reusable from cancelled orders

    @ui @api
    Scenario: Being unable to change code of promotion coupon
        When I want to modify the "SANTA2016" coupon for this promotion
        Then I should not be able to edit its code
