@managing_promotions
Feature: Adding a new promotion with rule
    In order to give possibility to pay less for some goods based on specific configuration
    As an Administrator
    I want to add a new promotion with rule to the registry

    Background:
        Given the store operates on a single channel in "France"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new promotion with contains taxon rule
        Given I want to create a new promotion
        When I specify its code as "T_SHIRTS_PROMOTION"
        And I name it "T-Shirts promotion"
        And I add the "Contains taxon" rule configured with 4 "T-Shirts"
        And I add it
        Then I should be notified that it has been successfully created
        And the "T-Shirts promotion" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a new promotion with taxon rule
        Given I want to create a new promotion
        When I specify its code as "HOLIDAY_SALE"
        And I name it "Holiday sale"
        And I add the "Taxon" rule configured with "T-Shirts" and "Mugs"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Holiday sale" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a new promotion with total of items from taxon rule
        Given I want to create a new promotion
        When I specify its code as "100_MUGS_PROMOTION"
        And I name it "100 Mugs promotion"
        And I add the "Total of items from taxon" rule configured with 100 "Mugs"
        And I add it
        Then I should be notified that it has been successfully created
        And the "100 Mugs promotion" promotion should appear in the registry
