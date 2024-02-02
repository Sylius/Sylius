@customer_registration
Feature: Receiving a welcoming email after registration
    In order to receive proof that my account has been created
    As a Visitor
    I want to receive the welcoming email

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales

    @ui @email @api
    Scenario: Receiving a welcoming email after registration when channel has disabled registration verification
        Given on this channel account verification is not required
        When I register with email "ghastly@bespoke.com" and password "suitsarelife"
        Then only one email should have been sent to "ghastly@bespoke.com"
        And a welcoming email should have been sent to "ghastly@bespoke.com"

    @ui @email @api
    Scenario: Receiving an account verification email after registration when channel has enabled registration verification
        Given on this channel account verification is required
        When I register with email "ghastly@bespoke.com" and password "suitsarelife"
        Then only one email should have been sent to "ghastly@bespoke.com"
        And a verification email should have been sent to "ghastly@bespoke.com"
        But a welcoming email should not have been sent to "ghastly@bespoke.com"

    @ui @email @api
    Scenario: Receiving a welcoming email after registration in different locale than the default one
        Given on this channel account verification is not required
        When I register with email "ghastly@bespoke.com" and password "suitsarelife" in the "Polish (Poland)" locale
        Then a welcoming email should have been sent to "ghastly@bespoke.com" in "Polish (Poland)" locale

    @ui @email @api
    Scenario: Receiving a welcoming email after account verification when channel has enabled registration verification
        Given on this channel account verification is required
        And I register with email "ghastly@bespoke.com" and password "suitsarelife"
        When I verify my account using link sent to "ghastly@bespoke.com"
        Then a welcoming email should have been sent to "ghastly@bespoke.com"
