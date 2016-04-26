@managing_taxons
Feature: Browsing taxons
    In order to see all taxons in the store
    As an Administrator
    I want to browse taxons

    Background:
        Given the store classifies its products as "T-Shirts" and "Accessories"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing taxons in store
        Given I want to see all taxons in store
        Then I should see 2 taxons on the list
        And I should see the taxon named "T-Shirts" in the list
