@resend_verification_email
Feature: Resending a verification email
    In order to verify my account when I did not receive the verification email
    As a Visitor
    I need to be able to request a new verification email

    Background:
        Given the store operates on a single channel in "United States"
        And there is a user "shop@example.com" identified by "sylius"
        And this user is not verified

    @api @email @no-ui
    Scenario: Resending a verification email to an existing unverified account
        When I resend the verification email to "shop@example.com"
        Then I should be notified that the verification email has been sent to the provided address
        And a verification email should have been sent to "shop@example.com"

    @api @email @no-ui
    Scenario: Not revealing whether an account exists when the email is not registered
        When I resend the verification email to "does-not-exist@example.com"
        Then I should be notified that the verification email has been sent to the provided address
        But "does-not-exist@example.com" should receive no emails

    @api @email @no-ui
    Scenario: Not sending a verification email to an already verified account
        Given the account of "shop@example.com" has been verified
        When I resend the verification email to "shop@example.com"
        Then I should be notified that the verification email has been sent to the provided address
        But "shop@example.com" should receive no emails

    @ui @no-api
    Scenario: Being informed about unverified account when trying to log in
        Given on this channel account verification is required
        When I log in as "shop@example.com" with "sylius" password
        Then I should be notified that my account has not been verified
        And I should see the resend verification email link
        And I should not be logged in

    @ui @email @no-api
    Scenario: Resending a verification email from the login page
        Given on this channel account verification is required
        When I log in as "shop@example.com" with "sylius" password
        And I resend the verification email from the login page
        Then I should be notified that the verification email has been sent to the provided address
        And a verification email should have been sent to "shop@example.com"
