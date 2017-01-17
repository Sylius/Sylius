@customer_account
Feature: Customer password validation
    In order to avoid making mistakes when changing my password
    As a Customer
    I want to be prevented from entering incorrect password

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer account "francis@underwood.com" identified by "whitehouse"
        And I am logged in as "francis@underwood.com"

    @ui
    Scenario: Trying to change my password with a wrong current password
        When I want to change my password
        And I specify the current password as "greenhouse"
        And I specify the new password as "blackhouse"
        And I confirm this password as "blackhouse"
        And I try to save my changes
        Then I should be notified that provided password is different than the current one

    @ui
    Scenario: Trying to change my password with a wrong confirmation password2
        When I want to change my password
        And I specify the current password as "whitehouse"
        And I specify the new password as "blackhouse"
        And I confirm this password as "greenhouse"
        And I try to save my changes
        Then I should be notified that the entered passwords do not match

    @ui
    Scenario: Trying to change my password with a too short password
        When I want to change my password
        And I specify the current password as "whitehouse"
        And I specify the new password as "fu"
        And I confirm this password as "fu"
        And I try to save my changes
        Then I should be notified that the password should be at least 4 characters long
