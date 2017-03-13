@managing_taxons
Feature: Taxons autocomplete
    In order to get hints when looking for taxons
    As an Administrator
    I want to get taxons according to my specified phrase

    Background:
        Given the store classifies its products as "T-Shirts", "Watches", "Belts" and "Wallets"
        And I am logged in as an administrator

    @api
    Scenario: Getting a hint when looking for taxons
        When I look for a taxon with "b" in name
        Then I should see 1 taxons on the list
        And I should see the taxon named "Belts" in the list

    @api
    Scenario: Getting a hint when looking for taxons
        When I look for a taxon with "shi" in name
        Then I should see 1 taxons on the list
        And I should see the taxon named "T-Shirts" in the list

    @api
    Scenario: Getting a hint when looking for taxons
        When I look for a taxon with "e" in name
        Then I should see 3 taxons on the list
        And I should see the taxon named "Belts", "Wallets" and "Watches" in the list

    @api
    Scenario: Getting a taxon from its code
        When I want to get taxon with "belts" code
        Then I should see 1 taxons on the list
        And I should see the taxon named "Belts" in the list
