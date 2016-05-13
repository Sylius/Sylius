@email_verification
Feature: Verifying account's email address
    In order to take full advantage of my account
    As a User
    I want to be able to verify my email address

    Background:
        Given the store operates on a single channel in "France"

    @todo
    Scenario: Receiving account verification email after registration
        When I register with email "bob@example.com" and password "drowssap"
        Then an email address verification email should be sent to "bob@example.com"
        And I should be logged in
        But my email address should not be verified yet

    @todo
    Scenario: Getting verified after clicking the link in the verification message
        Given I am not logged in
        And I am not a verified user "bob@example.com"
        When I use the verification email to verify
        Then I should be logged in
        And I should be notified that the verification was successful

    @todo
    Scenario: Resending the verification email as a logged in user
        Given I am logged in as "bob@example.com"
        When I resend the verification email
        Then an email should be sent to "bob@example.com"

    @todo
    Scenario: Being unable to verify using previous verification links
        Given I am logged in as "bob@emample.com"
        And the verification email has already been sent
        But I didn't verify
        When I resend the verification email
        But I use the first email to verify
        Then I should be notified that the verification was not successful

    @todo
    Scenario: Being unable to resend verification when verified
        Given I have already verified my account
        Then I should be unable to resend the verification email
