@taxation
Feature: Deleting a tax category
    In order to remove test, obsolete or incorrect tax categories
    As an Administrator
    I want to be able to delete a tax category

    Background:
        Given the store has "Alcohol" tax category with code "alcohol"

    @todo
    Scenario: Deleted tax category should disappear from the registry
        When I delete tax category "Alcohol"
        Then this tax category should no longer exist in the registry
