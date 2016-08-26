@customer_login
Feature: Resetting a password validation
    In order to avoid making mistakes when resetting password
    As a Visitor
    I need to be prevented from making mistakes in my address email

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Trying to reset password without specifying email
        Given I want to reset password
        When I do not specify the email
        And I reset it
        Then I should be notified that the email is required
