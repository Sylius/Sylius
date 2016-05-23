@customer_account
Feature: Changing a customer password
    In order to enhance the security of my account
    As a Customer
    I want to be able to change my password

    Background:
        Given the store operates on a single channel in "France"
        And there is a customer account "Francis Underwood" with email "francis@underwood.com" identified by "whitehouse"
        And I am logged in as "francis@underwood.com"

    @todo
    Scenario: Changing my email
        Given I want to change my password
        And I change password to "blackhouse"
        And I save my changes
        Then I should be notified that my password has been successfully changes
