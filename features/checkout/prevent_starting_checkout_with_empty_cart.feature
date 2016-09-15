@checkout
Feature: Prevent starting checkout with empty cart
    In order to proceed through the checkout correctly
    As a Customer
    I want to be prevented from accessing checkout with empty cart

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Being on shop home page after trying to start checkout addressing step with empty cart
        When I try to open checkout addressing page
        Then I should be redirected to my cart summary page

    @ui
    Scenario: Being on shop home page after trying to start checkout shipping step with empty cart
        When I try to open checkout shipping page
        Then I should be redirected to my cart summary page

    @ui
    Scenario: Being on shop home page after trying to start checkout payment step with empty cart
        When I try to open checkout payment page
        Then I should be redirected to my cart summary page

    @ui
    Scenario: Being on shop home page after trying to start checkout complete step with empty cart
        When I try to open checkout complete page
        Then I should be redirected to my cart summary page
