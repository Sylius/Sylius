@managing_taxons
Feature: Case-insensitive parent taxon autocomplete search
    In order to easily find parent taxons regardless of typing case
    As an Administrator
    I want to search for parent taxons in a case-insensitive manner

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "Tech Gadgets" with "TECH_GADGETS" code
        And the store classifies its products as "Home Decor" with "home_DECOR" code
        And the store classifies its products as "Sports Equipment" with "SPORTS_equipment" code
        And the store classifies its products as "Office Supplies" with "office_supplies" code
        And the store classifies its products as "Tech" with "TECH" code
        And I am logged in as an administrator

    @no-api @ui @mink:chromedriver
    Scenario Outline: Case-insensitive parent taxon search with various inputs
        When I want to create a new taxon
        And I search for parent taxon "<search_term>"
        Then I should see "<expected_result>" in the found results

        Examples:
            | search_term      | expected_result   |
            | tech_gadgets     | Tech Gadgets      |
            | HOME_DECOR       | Home Decor        |
            | sports_EQUIPMENT | Sports Equipment  |
            | office supplies  | Office Supplies   |
            | TECH GADGETS     | Tech Gadgets      |
            | SPORTS           | Sports Equipment  |
            | home             | Home Decor        |
            | GADGET           | Tech Gadgets      |
            | equipment        | Sports Equipment  |

    @no-api @ui @mink:chromedriver
    Scenario: Searching returns multiple matching taxons
        When I want to create a new taxon
        And I search for parent taxon "tech"
        Then I should see "Tech" in the found results
        And I should see "Tech Gadgets" in the found results
