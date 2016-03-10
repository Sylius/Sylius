@legacy @metadata
Feature: Metadata management
    In order to manage metadata on my store
    As a store owner
    I want to have easy and intuitive access to managing metadata

    Background:
        Given store has default configuration
        And I am logged in as administrator

    Scenario: Accessing default metadata customization page
        Given I am on the metadata container index page
        When I click "Customize default metadata"
        Then I should be customizing default metadata
