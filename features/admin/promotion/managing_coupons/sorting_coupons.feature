@managing_promotion_coupons
Feature: Sorting listed coupons
    In order to change the order by which coupons are displayed
    As an Administrator
    I want to sort promotion coupons

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

    @ui @api
    Scenario: Coupons are sorted by descending number of uses by default
        When I want to view all coupons of this promotion
        Then I should see 3 coupons on the list
        And the first coupon should have code "Y"

    @ui @api
    Scenario: Changing the number of uses sorting order to ascending
        Given I am browsing coupons of this promotion
        When I sort coupons by ascending number of uses
        Then I should see 3 coupons on the list
        And the first coupon should have code "X"

    @ui @api
    Scenario: Sorting coupons by code in descending order
        Given I am browsing coupons of this promotion
        When I sort coupons by descending code
        Then I should see 3 coupons on the list
        And the first coupon should have code "Z"

    @ui @api
    Scenario: Sorting coupons by code in ascending order
        Given I am browsing coupons of this promotion
        When I sort coupons by ascending code
        Then I should see 3 coupons on the list
        And the first coupon should have code "X"

    @ui @no-postgres @api
    Scenario: Sorting coupons by usage limit in descending order
        Given I am browsing coupons of this promotion
        When I sort coupons by descending usage limit
        Then I should see 3 coupons on the list
        And the first coupon should have code "X"

    @ui @no-postgres @api
    Scenario: Sorting coupons by usage limit in ascending order
        Given I am browsing coupons of this promotion
        When I sort coupons by ascending usage limit
        Then I should see 3 coupons on the list
        And the first coupon should have code "Z"

    @ui @no-postgres @api
    Scenario: Sorting coupons by usage limit per customer in descending order
        Given I am browsing coupons of this promotion
        When I sort coupons by descending usage limit per customer
        Then I should see 3 coupons on the list
        And the first coupon should have code "X"

    @ui @no-postgres @api
    Scenario: Sorting coupons by usage limit per customer in ascending order
        Given I am browsing coupons of this promotion
        When I sort coupons by ascending usage limit per customer
        Then I should see 3 coupons on the list
        And the first coupon should have code "Y"

    @ui @no-postgres @api
    Scenario: Sorting coupons by expiration date in descending order
        Given I am browsing coupons of this promotion
        When I sort coupons by descending expiration date
        Then I should see 3 coupons on the list
        And the first coupon should have code "Z"

    @ui @no-postgres @api
    Scenario: Sorting coupons by expiration date in ascending order
        Given I am browsing coupons of this promotion
        When I sort coupons by ascending expiration date
        Then I should see 3 coupons on the list
        And the first coupon should have code "Y"
