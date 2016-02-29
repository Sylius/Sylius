@promotion
Feature: Deleting a coupon
    In order to remove test, obsolete or incorrect coupons
    As an Administrator
    I want to be able to delete coupons from the registry

    Background:
        Given the store operates on a single channel in "France"
        And the store has promotion "Christmas sale" with coupon "Santa's gift"

    @domain
    Scenario: Deleted coupon should disappear from the registry
        When I delete "Santa's Gift" coupon
        Then this coupon should no longer exist in the registry
