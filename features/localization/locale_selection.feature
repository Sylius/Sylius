@localization
Feature: Locale selection
    In order to browse the website in my preferred language
    As a customer
    I want to to select my language in the storefront

    Background:
        And there are following locales configured:
            | code  | enabled |
            | de_DE | yes     |
            | en_US | yes     |
            | fr_FR | no      |
            | pl_PL | yes     |
        And there is default currency configured

    Scenario: Only enabled locales are visible to the user
        Given I am on the store homepage
        Then I should see 3 available locales
        And I should browse the store in English

    Scenario: Changing the locale in storefront
        Given I am on the store homepage
        When I change the locale to Polish
        Then I should browse the store in Polish

    Scenario: Switching the locale as a logged in customer
        Given I am logged in user
        And I am on the store homepage
        When I change the locale to German
        Then I should browse the store in German
