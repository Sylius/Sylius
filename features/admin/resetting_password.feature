@administrator_login
Feature: Resetting an administrator's password
    In order to login to my administrator account when I forget my password
    As a not logged in Administrator
    I want to be able to reset my password

    Background:
        Given there is an administrator "sylius@example.com" identified by "sylius"

    @email @api @ui
    Scenario: Sending an administrator's password reset request
        When I want to reset password
        And I specify email as "sylius@example.com"
        And I reset it
        Then I should be notified that email with reset instruction has been sent
        And an email with instructions on how to reset the administrator's password should be sent to "sylius@example.com"

    @todo
    Scenario: Changing my administrator's password
        Given I have already received an administrator's password resetting email
        When I reset my password using the received instructions
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should be notified that my password has been successfully changed
        And I should be able to log in as "sylius@example.com" with "newp@ssw0rd" password

    @todo
    Scenario: Trying to change my administrator's password twice without sending a new password reset request
        Given I already reset my administrator's password
        When I try to reset my password again using the same email
        Then I should not be able to change it again without sending a new password reset request
