@customer_account
Feature: Changing a customer password
    In order to enhance the security of my account
    As a Customer
    I want to be able to change my password

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
        And I am logged in as "francis@underwood.com"

    @ui
    Scenario: Changing my password
        When I want to change my password
        And I change password from "whitehouse" to "blackhouse"
        And I save my changes
        Then I should be notified that my password has been successfully changed
