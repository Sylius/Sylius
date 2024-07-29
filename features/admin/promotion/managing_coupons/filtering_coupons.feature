@managing_promotion_coupons
Feature: Filtering coupons
    In order to quickly find promotion coupons
    As an Administrator
    I want to be able to filter promotion coupons in the list

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Special Sale"
        And this promotion has coupon "X"
        And this coupon can be used 3 times per customer with overall usage limit of 42
        And this coupon expires on "12-01-2023"
        And this promotion has coupon "Y"
        And this coupon can be used 5 times
        And this coupon has been used 3 times
        And this promotion has coupon "Z"
        And this coupon can be used 1 time per customer
        And this coupon has been used 1 time
        And this coupon expires on "20-02-2023"
        And I am logged in as an administrator

    @api @ui
    Scenario: Filtering coupons by code
        Given I am browsing coupons of this promotion
        When I filter by code containing "X"
        Then I should see a single promotion coupon in the list
        And I should see the promotion coupon "X" in the list
