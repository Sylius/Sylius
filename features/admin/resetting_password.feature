@administrator_login
Feature: Resetting an administrator's password
    In order to login to my administrator account when I forget my password
    As an not logged in Administrator
    I need to be able to reset my password

    Background:
        Given there is an administrator "sylius@example.com" identified by "sylius"

    @todo
    Scenario: Resetting an administrator password
        When I want to reset password
        And I specify email as "sylius@example.com"
        And I reset it
        Then I should be notified that email with reset instruction has been sent
        And an email with reset token should be sent to "sylius@example.com"

    @todo
    Scenario: Changing my administrator password with a token I received
        Given I have already received a resetting administrator's password email
        When I follow link on my email to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should be notified that my password has been successfully changed
        And I should be able to log in as "sylius@example.com" with "newp@ssw0rd" password

    @todo
    Scenario: Trying to change my administrator password twice with a token I received
        Given I have already received a resetting administrator's password email
        When I follow link on my email to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "newp@ssw0rd"
        And I reset it
        Then I should not be able to change my password again with the same token
