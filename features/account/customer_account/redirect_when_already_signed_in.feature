@customer_account
Feature: Redirect when already signed in
    In order to be aware that I am already logged in
    As a Customer
    I want to be redirected to account panel dashboard when accessing the login page

    @ui
    Scenario: Trying to access login page as logged in user
        Given the store operates on a single channel in "United States"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
        And I am logged in as "francis@underwood.com"
        When I want to log in
        Then I should be redirected to my account dashboard
