@promotion
Feature: Deleting a coupon
    In order to remove test, obsolete or incorrect coupons
    As an Administrator
    I want to be able to delete coupons from the registry

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Holiday promotion" with coupon "Santa's gift"
        And I am logged in as administrator

    @todo
    Scenario: Deleted coupon should disappear from the registry
        When I delete "Santa's Gift" coupon
        Then this coupon should no longer exist in the registry
