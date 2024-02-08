@applying_catalog_promotions
Feature: Reapplying catalog promotion after editing their scopes
    In order to have proper discounts per catalog promotion action
    As a Store Owner
    I want to have catalog promotion reapplied in product catalog once the scope of catalog promotion changes

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the store has a "Mug" configurable product
        And this product belongs to "Dishes"
        And this product has "PHP Mug" variant priced at "$5.00"
        And this product has "Expensive Mug" variant priced at "$50.00"
        And there is a catalog promotion "Summer sale" that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And I am logged in as an administrator

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after adding a new scope to it
        Given there is a catalog promotion named "Mug sale"
        When I modify a catalog promotion "Mug sale"
        And I add scope that applies on "Expensive Mug" variant and "PHP Mug" variant
        And I add action that gives "50%" percentage discount
        And I save my changes
        Then the visitor should see that the "Expensive Mug" variant is discounted from "$50.00" to "$25.00" with "Mug sale" promotion
        And the visitor should see that the "PHP Mug" variant is discounted from "$5.00" to "$2.50" with "Mug sale" promotion

    @api @ui @javascript
    Scenario: Reapplying catalog promotion after removing its scopes
        When I modify a catalog promotion "Summer sale"
        And I remove its every scope
        And I save my changes
        Then the visitor should see that the "PHP T-Shirt" variant is not discounted

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after adding another scope
        When I modify a catalog promotion "Summer sale"
        And I add another scope that applies on "PHP Mug" variant
        And I save my changes
        Then the visitor should see that the "PHP Mug" variant is discounted from "$5.00" to "$2.50" with "Summer sale" promotion
        And the visitor should still see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$10.00" with "Summer sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after editing the variant in its scope
        When I edit "Summer sale" catalog promotion to be applied on "Expensive Mug" variant
        Then the visitor should see that the "Expensive Mug" variant is discounted from "$50.00" to "$25.00" with "Summer sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is not discounted

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after editing the taxon in its scope
        Given there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "Clothes" taxon
        When I edit "Winter sale" catalog promotion to be applied on "Dishes" taxon
        Then the visitor should see that the "PHP Mug" variant is discounted from "$5.00" to "$3.50" with "Winter sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$10.00" with "Summer sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after editing the product in its scope
        Given there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "T-Shirt" product
        When I edit "Winter sale" catalog promotion to be applied on "Mug" product
        Then the visitor should see that the "PHP Mug" variant is discounted from "$5.00" to "$3.50" with "Winter sale" promotion
        And the visitor should see that the "Expensive Mug" variant is discounted from "$50.00" to "$35.00" with "Winter sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$10.00" with "Summer sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Reapplying catalog promotion after editing the type of its scope
        Given there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant
        When I edit "Winter sale" catalog promotion to be applied on "Dishes" taxon
        Then the visitor should see that the "PHP Mug" variant is discounted from "$5.00" to "$3.50" with "Winter sale" promotion
        And the visitor should see that the "PHP T-Shirt" variant is discounted from "$20.00" to "$10.00" with "Summer sale" promotion
