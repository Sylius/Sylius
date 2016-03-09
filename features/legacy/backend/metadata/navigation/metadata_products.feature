@legacy @metadata
Feature: Metadata management
    In order to manage metadata on my store
    As a store owner
    I want to have easy and intuitive access to managing metadata

    Background:
        Given store has default configuration
        And there are products:
            | name   | price |
            | Banana | 4.20  |
        And I am logged in as administrator

    Scenario: Accessing products metadata customization page
        Given I am on the product index page
        When I click "Customize products metadata"
        Then I should be customizing products metadata

    Scenario: Accessing specific product metadata customization page via index page
        Given I am on the product index page
        When I click "Customize metadata" near "Banana"
        Then I should be customizing specific product metadata

    Scenario: Accessing specific product metadata customization page via product page
        Given I am on the page of product "Banana"
        When I click "Customize metadata"
        Then I should be customizing specific product metadata
