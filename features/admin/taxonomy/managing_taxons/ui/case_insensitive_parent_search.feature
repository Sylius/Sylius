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
        And I am logged in as an administrator

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon by lowercase code when taxon code has uppercase letters
        When I want to create a new taxon
        And I search for parent taxon "tech_gadgets"
        Then I should see "Tech Gadgets" in the found results

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon by uppercase code when taxon code has lowercase letters
        When I want to create a new taxon
        And I search for parent taxon "HOME_DECOR"
        Then I should see "Home Decor" in the found results

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon by mixed case code when taxon code has different case
        When I want to create a new taxon
        And I search for parent taxon "sports_EQUIPMENT"
        Then I should see "Sports Equipment" in the found results

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon by lowercase name when taxon name has mixed case
        When I want to create a new taxon
        And I search for parent taxon "office supplies"
        Then I should see "Office Supplies" in the found results

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon by uppercase name when taxon name has mixed case
        When I want to create a new taxon
        And I search for parent taxon "TECH GADGETS"
        Then I should see "Tech Gadgets" in the found results

    @no-api @ui @mink:chromedriver
    Scenario: Searching for parent taxon with partial match case-insensitive on code
        When I want to create a new taxon
        And I search for parent taxon "SPORTS"
        Then I should see "Sports Equipment" in the found results
