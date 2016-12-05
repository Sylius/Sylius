@managing_promotions
Feature: Adding a new promotion with rule
    In order to give possibility to pay less for some goods based on specific configuration
    As an Administrator
    I want to add a new promotion with rule to the registry

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new promotion with taxon rule
        Given I want to create a new promotion
        When I specify its code as "HOLIDAY_SALE"
        And I name it "Holiday sale"
        And I add the "Has at least one from taxons" rule configured with "T-Shirts" and "Mugs"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Holiday sale" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a new promotion with total price of items from taxon rule
        Given I want to create a new promotion
        When I specify its code as "100_MUGS_PROMOTION"
        And I name it "100 Mugs promotion"
        And I add the "Total price of items from taxon" rule configured with "Mugs" taxon and $100 amount for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "100 Mugs promotion" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a new promotion with contains product rule
        Given the store has a product "PHP T-Shirt" priced at "$100.00"
        And I want to create a new promotion
        When I specify its code as "PHP_TSHIRT_PROMOTION"
        And I name it "PHP T-Shirt promotion"
        And I add the "Contains product" rule configured with the "PHP T-Shirt" product
        And I add it
        Then I should be notified that it has been successfully created
        And the "PHP T-Shirt promotion" promotion should appear in the registry

    @ui @javascript
    Scenario: Adding a new group based promotion
        Given the store has a customer group "Wholesale"
        When I want to create a new promotion
        And I specify its code as "WHOLESALES_PROMOTION"
        And I name it "Wholesale promotion"
        And I add the "Customer group" rule for "Wholesale" group
        And I add it
        Then I should be notified that it has been successfully created
        And the "Wholesale promotion" promotion should appear in the registry
