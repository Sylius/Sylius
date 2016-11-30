@managing_taxons
Feature: Reordering taxons
    In order to see all ordered taxons in the store
    As an Administrator
    I want to browse ordered taxons

    Background:
        Given the store classifies its products as "T-Shirts", "Watches", "Belts" and "Wallets"
        And I am logged in as an administrator

    @ui @javascript @todo
    Scenario: Moving up the taxon on list
        When I want to see all taxons in store
        And I move up "Watches" taxon
        Then I should see 4 taxons on the list
        And I should see the taxon named "T-Shirts" in the list
        But the first taxon on the list should be "Watches"

    @ui @javascript @todo
    Scenario: Moving down the taxon on list
        When I want to see all taxons in store
        And I move down "T-Shirts" taxon
        Then I should see 4 taxons on the list
        And I should see the taxon named "Watches" in the list
        But the first taxon on the list should be "Watches"

    @ui @javascript @todo
    Scenario: Changing order of the taxon on list
        When I want to see all taxons in store
        And I move down "T-Shirts" taxon
        And I move down "Belts" taxon
        And I move up "Wallets" taxon
        Then I should see 4 taxons on the list
        And they should have order like "Watches", "Wallets", "T-Shirts" and "Belts"
