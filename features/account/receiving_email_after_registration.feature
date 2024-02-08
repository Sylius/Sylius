@customer_registration
Feature: Receiving a welcoming email after registration
    In order to receive proof that my account has been created
    As a Visitor
    I want to receive the welcoming email

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales

    @ui @email @api
    Scenario: Receiving a welcoming email after registration
        When I register with email "ghastly@bespoke.com" and password "suitsarelife"
        Then a welcoming email should have been sent to "ghastly@bespoke.com"

    @ui @email @api
    Scenario: Receiving a welcoming email after registration in different locale than the default one
        When I register with email "ghastly@bespoke.com" and password "suitsarelife" in the "Polish (Poland)" locale
        Then a welcoming email should have been sent to "ghastly@bespoke.com" in "Polish (Poland)" locale
