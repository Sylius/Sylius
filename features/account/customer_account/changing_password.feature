@customer_account
Feature: Changing a customer password
    In order to enhance the security of my account
    As a Customer
    I want to be able to change my password

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "sylius"
        And I am logged in as "francis@underwood.com"

    @ui @api
    Scenario: Changing my password
        When I want to change my password
        And I change password from "sylius" to "blackhouse"
        And I save my changes
        Then I should be notified that my password has been successfully changed

    @api @ui
    Scenario: Logging to store after password change
        Given I've changed my password from "whitehouse" to "blackhouse"
        When I log in as "francis@underwood.com" with "blackhouse" password
        Then I should be logged in
