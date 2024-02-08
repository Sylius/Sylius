@administrator_login
Feature: Resetting an administrator's password
    In order to login to my administrator account when I forget my password
    As a not logged in Administrator
    I want to be able to reset my password

    Background:
        Given the store operates on a single channel in "United States"
        And there is an administrator "sylius@example.com" identified by "sylius"

    @email @api @ui
    Scenario: Sending an administrator's password reset request
        When I want to reset password
        And I specify email as "sylius@example.com"
        And I reset it
        Then I should be notified that email with reset instruction has been sent
        And an email with instructions on how to reset the administrator's password should be sent to "sylius@example.com"

    @email @api @ui
    Scenario: Notifying about sending reset instructions even when an admin with email does not exist
        When I want to reset password
        And I specify email as "does-not-exist@example.com"
        And I reset it
        Then I should be notified that email with reset instruction has been sent
        But "does-not-exist@example.com" should receive no emails

    @ui @api
    Scenario: Changing my administrator's password
        Given I have already received a resetting password email
        When I follow the instructions to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should be notified that my password has been successfully changed
        And I should be able to log in as "sylius@example.com" authenticated by "newp@ssw0rd" password

    @ui @api
    Scenario: Trying to change my administrator's password twice without sending a new password reset request
        Given I have already received an administrator's password resetting email
        When I follow the instructions to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should not be able to change my password again with the same token

    @api
    Scenario: Trying to change my administrator's password using an expired reset token
        Given I have already received an administrator's password resetting email
        But my password reset token has already expired
        When I try to follow the instructions to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should be notified that the password reset token has expired

    @ui @no-api
    Scenario: Trying to change my administrator's password using an expired reset token
        Given I have already received an administrator's password resetting email
        But my password reset token has already expired
        When I try to follow the instructions to reset my password
        Then I should be notified that the password reset token has expired
