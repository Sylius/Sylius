@administrator_login
Feature: Resetting an administrator's password validation
    In order to avoid making mistakes when resetting administrator's password
    As a not logged in Administrator
    I want to be prevented from making mistakes in my address email

    Background:
        Given there is an administrator "sylius@example.com" identified by "sylius"

    @ui @api
    Scenario: Trying to reset my administrator's password without specifying email
        When I want to reset password
        And I do not specify an email
        And I try to reset it
        Then I should be notified that the email is required

    @ui @api
    Scenario: Trying to reset my administrator's password with an empty value
        Given I have already received an administrator's password resetting email
        When I follow the instructions to reset my password
        And I do not specify my new password
        And I do not confirm my new password
        And I try to reset it
        Then I should be notified that the new password is required

    @ui @api
    Scenario: Trying to reset my administrator's password with an invalid email
        When I want to reset password
        And I specify email as "sylius@examplecom"
        And I try to reset it
        Then I should be notified that the email is not valid

    @ui @api
    Scenario: Trying to reset my administrator's password with a wrong confirmation password
        Given I have already received an administrator's password resetting email
        When I follow the instructions to reset my password
        And I specify my new password as "newp@ssw0rd"
        And I confirm my new password as "wrongp@ssw0rd"
        And I try to reset it
        Then I should be notified that the entered passwords do not match

    @ui @api
    Scenario: Trying to reset my administrator's password with a too short password
        Given I have already received an administrator's password resetting email
        When I follow the instructions to reset my password
        And I specify my new password as "fu"
        And I confirm my new password as "fu"
        And I try to reset it
        Then I should be notified that the password should be at least 4 characters long
