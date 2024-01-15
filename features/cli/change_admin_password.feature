@change_admin_password
Feature: Changing an administrator's password via CLI
    In order to login to my administrator account when I forget my password
    As a administrator
    I want to be able to change my administrator password via CLI

    Background:
        Given there is an administrator "sylius_change_password@example.com" identified by "sylius"

    @cli
    Scenario: Changing an administrator's password
        When I want to change password
        And I specify email as "sylius_change_password@example.com"
        And I specify my new password as "newp@ssw0rd"
        And I run command
        Then I should be informed that password has been changed successfully
        And I should be able to log in as "sylius_change_password@example.com" authenticated by "newp@ssw0rd" password
