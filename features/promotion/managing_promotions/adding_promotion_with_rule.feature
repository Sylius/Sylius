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
        And the promotion "T-Shirts promotion" should appear in the registry
