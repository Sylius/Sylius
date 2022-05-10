@customer_account
Feature: Securing access to the account after using the back button after logging out
    In order to have my personal information secured
    As a Customer
    I want to be unable to access to the account by using the back button after logging out

    Background:
        Given the store operates on a single channel in "United States"
        And I am a logged in customer
        And I am browsing my orders

    @ui @javascript @no-api
    Scenario: Securing access to the account after using the back button after logging out
        When I log out
        And I go back one page in the browser
        Then I should not see my orders
        And I should be on the login page
