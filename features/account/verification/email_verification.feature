@email_verification
Feature: Verifying account's email address
    In order to take full advantage of my account
    As a User
    I want to be able to verify my email address

    Background:
        Given the store operates on a single channel in "France"
        And there is user "bob@example.com" identified by "drowsapp"
        And his account is not verified

    @ui
    Scenario: Getting verified after clicking the link in the verification message
        Given the verification email has already been sent to "bob@example.com"
        When I use it to verify
        Then I should be notified that the verification was successful
        And I should be logged in as "bob@example.com"
        And my account should be verified

    @ui
    Scenario: Being unable to verify with invalid token
        When I try to verify using email "bob@example.com" and token "twinklelittlestar"
        Then I should not be logged in
        And I should be notified that the verification was not successful

    @ui
    Scenario: Resending the verification email as a logged in user
        Given I am logged in as "bob@example.com"
        When I resend the verification email
        Then I should be notified that the verification email has been sent
        And an email should be sent to "bob@example.com"

    @ui
    Scenario: Being unable to verify using old verification links
        Given I am logged in as "bob@example.com"
        And I have already received the verification email
        But I have not verified my account yet
        When I resend the verification email
        But I use the first email to verify
        Then I should be notified that the verification was not successful
        And my account should not be verified

    @ui
    Scenario: Being unable to resend verification token when verified
        Given I am logged in as "bob@example.com"
        And I have already verified my account
        Then I should be unable to resend the verification email

    @todo
    Scenario: Receiving account verification email after registration
        When I register with email "ted@example.com" and password "aliceinwonderland"
        Then I should be logged in
        And I should be notified that my account has been created and the verification email has been sent
        And the verification email should be sent to "ted@example.com"
        But my account should not be verified yet
