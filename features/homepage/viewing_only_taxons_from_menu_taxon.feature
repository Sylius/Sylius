@homepage
Feature: Viewing only taxons from the menu taxon
    In order to be able to browse only products from the correct taxon
    As a Visitor
    I want to see taxon list based on the current channel's menu taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Category", "Clothes" and "Guns"
        And the "Category" taxon has children taxons "Pens" and "Pencils"
        And the "Clothes" taxon has children taxons "T-Shirts" and "Caps"
        And the "Guns" taxon has children taxons "Rifles" and "Revolvers"

    @ui
    Scenario: Viewing taxons list only from the channel menu taxon
        Given this channel has menu taxon "Guns"
        When I visit the homepage
        Then I should see "Rifles" and "Revolvers" in the menu
        And I should not see "Pens" and "Pencils" in the menu
        And I should not see "T-Shirts" and "Caps" in the menu

    @ui
    Scenario: Using general taxon if channel does not have a menu taxon specified
        When I visit the homepage
        Then I should see "Pens" and "Pencils" in the menu
        And I should not see "T-Shirts" and "Guns" in the menu
        And I should not see "Rifles" and "Revolvers" in the menu

    @ui
    Scenario: Seeing correct taxons after switching the channel
        Given the store operates on another channel named "Poland"
        And channel "United States" has menu taxon "Guns"
        And channel "Poland" has menu taxon "Clothes"
        When I change my current channel to "Poland"
        And I visit the homepage
        Then I should see "T-Shirts" and "Caps" in the menu
        And I should not see "Rifles" and "Revolvers" in the menu
        And I should not see "Pens" and "Pencils" in the menu
