@managing_promotion_coupons
Feature: Deleting multiple coupons
    In order to remove test, obsolete or incorrect coupons in an efficient way
    As an Administrator
    I want to be able to delete multiple coupons at once from the registry

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And this promotion has "SANTA1", "SANTA2" and "SANTA3" coupons
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple coupons at once
        When I browse coupons of this promotion
        And I check the "SANTA1" coupon
        And I check also the "SANTA2" coupon
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single coupon in the list
        And I should see the coupon "SANTA3" in the list
