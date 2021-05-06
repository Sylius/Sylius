@customer_login
Feature: Seeing myself while login
    In order to be aware who I am
    As a Customer
    I want to see my basic data while login

    Background:
        Given the store operates on a single channel in "United States"
        And the store has customer "John Doe" with email "car@better.com"
        And I have already registered "car@better.com" account

    @api
    Scenario: Seeing my basic data while login
        When I log in with the email "car@better.com"
        Then I should see who I am
