@managing_taxons
Feature: Browsing taxons tree
    In order to see all taxons in the store
    As an Administrator
    I want to browse taxons

    Background:
        Given the store classifies its products as "T-Shirts", "Watches", "Belts" and "Wallets"
        And the "Watches" taxon has children taxon "Digital" and "Analog"
        And I am logged in as an administrator

    @api
    Scenario: Getting taxon root
        When I want to get taxon root
        Then I should see 4 taxons on the list
        And I should see the taxon named "Belts", "Wallets", "Watches" and "T-Shirts" in the list

    @api
    Scenario: Getting taxon leafs
        When I want to get children from taxon "Watches"
        Then I should see 2 taxons on the list
        And I should see the taxon named "Digital" and "Analog" in the list
