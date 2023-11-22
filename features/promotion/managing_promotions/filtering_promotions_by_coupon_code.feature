@managing_promotions
Feature: Filtering promotions by coupon code
    In order to filter promotions by coupon code
    As an Administrator
    I want to filter the promotions on the list

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Basic promotion"
        And the store has promotion "Holiday sale" with coupon "HOLIDAY"
        And the store has promotion "Christmas sale" with coupon "MAGIC"
        And I am logged in as an administrator

    @api @ui
    Scenario: Filtering promotions by coupon code
        When I want to browse promotions
        And I filter promotions by coupon code equal "MAGIC"
        And I should see a single promotion in the list
        And I should see the promotion "Christmas sale" in the list
