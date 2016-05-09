@managing_promotion_coupons
Feature: Deleting a coupon
    In order to remove test, obsolete or incorrect coupons
    As an Administrator
    I want to be able to delete a coupon from the registry

    Background:
        Given the store operates on a single channel in "France"
        And the store has promotion "Christmas sale" with coupon "Santa's gift"
        And I am logged in as an administrator

    @domain @ui
    Scenario: Deleted coupon should disappear from the registry
        When I delete "Santa's Gift" coupon related to this promotion
        Then I should be notified that it has been successfully deleted
        And this coupon should no longer exist in the coupon registry
