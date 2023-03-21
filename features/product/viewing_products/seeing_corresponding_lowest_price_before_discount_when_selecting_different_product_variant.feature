@viewing_products
Feature: Seeing the corresponding lowest price before the discount when selecting different product variants
    In order to be aware of the lowest price before the discount for the chosen product variant
    As a Customer
    I want to see the corresponding lowest price before the discount for the chosen product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka 40%" variant priced at "$40.00"
        And the product "Wyborowa Vodka" has "Wyborowa Vodka 50%" variant priced at "$50.00"
        And the product "Wyborowa Vodka" has "Wyborowa Vodka 30%" variant priced at "$60.00"
        And there is a catalog promotion "Winter sale" with priority 1 that reduces price by "50%" and applies on "Wyborowa Vodka" product

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting first variant from the list
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Vodka 40%" variant
        Then I should see "$40.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting another variant from the list
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Vodka 50%" variant
        Then I should see "$50.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting first variant from the list after selecting another variant
        When I view product "Wyborowa Vodka"
        And I select "Wyborowa Vodka 50%" variant
        And I select "Wyborowa Vodka 30%" variant
        And I select "Wyborowa Vodka 40%" variant
        Then I should see "$40.00" as its lowest price before the discount
