@customer_account
Feature: Signing out
    In order to not leave my account open to passersby
    As a Customer
    I want to be able to log out of the store

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I am browsing my orders

    @ui @no-api
    Scenario: Signing out
        When I log out
        Then I should be redirected to the homepage
        And I should not be logged in
