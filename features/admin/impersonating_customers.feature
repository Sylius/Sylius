@impersonating_customers
Feature: Impersonating shop users
    In order to provide a top-notch customer support
    As an Administrator
    I want to impersonate a shop user

    Background:
        Given the store operates on a single channel in "United States"
        And there is an administrator "teddy@roosevelt.com"
        And there is a customer "Tanith Low" identified by an email "remnant@london.uk" and a password "swordlover1918"
        And I am logged in as "teddy@roosevelt.com" administrator

    @ui
    Scenario: Impersonating a customer
        When I view details of the customer "remnant@london.uk"
        And I impersonate them
        And I visit the store
        Then I should be logged in as "Tanith Low"

    @ui
    Scenario: Inability to impersonate a customer with no account
        Given the store has customer "harold@thrasher.ie" with first name "Harold"
        When I view their details
        Then I should be unable to impersonate them

    @ui
    Scenario: Seeing the impersonation was successful
        When I view details of the customer "remnant@london.uk"
        And I impersonate them
        Then I should see that impersonating "remnant@london.uk" was successful

    @ui
    Scenario: Keeping the administrator access while impersonating a user
        When I view details of the customer "remnant@london.uk"
        And I impersonate them
        And I visit the store
        Then I should be logged in as "Tanith Low"
        But I should still be able to access the administration dashboard

    @ui
    Scenario: Logging out the user doesn't log out my admin account
        Given I am impersonating the customer "remnant@london.uk"
        When I log out from the store
        Then I should still be able to access the administration dashboard

    @ui
    Scenario: Logging out from my admin account logs me off the user I'm impersonating
        Given I am impersonating the customer "remnant@london.uk"
        When I log out from my admin account
        And I visit the store
        Then I should not be logged in as "Tanith Low"
