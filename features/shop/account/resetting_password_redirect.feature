@customer_login
Feature: Resetting a password from my personal Password Manager
    In order to login to my account when I forgot my password
    As a Visitor
    I need to be able to reset my password using my password manager

    @ui @no-api
    Scenario: Getting redirected to the forgotten password page
        Given the store operates on a single channel in "United States"
        When I want to reset password from my password manager
        Then I should be redirected to the forgotten password page
