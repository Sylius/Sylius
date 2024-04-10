@customer_registration
Feature: Account registration
    In order to make future purchases with ease
    As a Visitor
    I need to be able to create an account in the store

    Background:
        Given the store operates on a single channel in "United States"

    @ui @no-api
    Scenario: Registering a new account with minimum information when channel has enabled registration verification
        Given on this channel account verification is required
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
        And I should be on registration thank you page
        But I should not be logged in

    @api @no-ui
    Scenario: Registering a new account with minimum information when channel has enabled registration verification
        Given on this channel account verification is required
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
        But I should not be logged in

    @ui @api
    Scenario: Registering a new account with minimum information when channel has disabled registration verification
        Given on this channel account verification is not required
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
        And I should be logged in
        And I should be on my account dashboard

    @ui @api
    Scenario: Registering a new account with all details
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I specify the phone number as "123456789"
        And I register this account
        Then I should be notified that new account has been successfully created
        But I should not be logged in

    @ui @api
    Scenario: Registering a guest account
        Given there is a customer "goodman@gmail.com" that placed an order "#001"
        When I want to register a new account
        And I specify the first name as "Saul"
        And I specify the last name as "Goodman"
        And I specify the email as "goodman@gmail.com"
        And I specify the password as "heisenberg"
        And I confirm this password
        And I register this account
        Then I should be notified that new account has been successfully created
        But I should not be logged in
