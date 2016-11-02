@managing_taxons
Feature: Reordering taxons
    In order to see all ordered taxons in the store
    As an Administrator
    I want to browse ordered taxons

    Background:
        Given the store classifies its products as "T-Shirts" and "Watches"
        And the store classifies its products as "Belts" and "Wallets"
        And I am logged in as an administrator

    @todo
    Scenario: Changing order of the taxon
        Given I want to see all taxons in store
        When I want to move up "Watches" taxon
        Then I should see 2 taxons on the list
        And I should see the taxon named "T-Shirts" in the list
        But the first taxon on the list should be "Watches"
