@managing_shipping_categories
Feature: Browsing shipping categories
    In order to have a overview of all defined shipping categories
    As an Administrator
    I want to be able to browse list of them

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Over sized" and "Standard" shipping category
        And I am logged in as an administrator

    @ui
    Scenario: Browsing defined shipping categories
        When I browse shipping categories
        Then I should see 2 shipping categories in the list
        And the shipping category "Standard" should be in the registry
