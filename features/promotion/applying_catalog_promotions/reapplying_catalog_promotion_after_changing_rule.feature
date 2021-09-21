@applying_catalog_promotions
Feature: Reapplying catalog promotion after editing its rule
    In order to have proper discounts per catalog promotion action
    As a Store Owner
    I want to have catalog promotion reapplied in product catalog once the rule of catalog promotion changes

    Background:
        Given the store operates on a channel identified by "Web-US" code
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the store has a "Mug" configurable product
        And this product has "PHP Mug" variant priced at "$5.00"
        And this product has "Expensive Mug" variant priced at "$50.00"
        And there is a catalog promotion "Summer sale" that reduces price by "50%" and applies on "PHP T-shirt" variant
        And I am logged in as an administrator

    @api
    Scenario: Reapplying catalog promotion after adding its rule
        Given there is a catalog promotion with "mug_sale" code and "Mug sale" name
        When I want to modify a catalog promotion "Mug sale"
        And I add rule that applies on variants "Expensive Mug" variant and "PHP Mug" variant
        And I add action that gives "50%" percentage discount
        And I save my changes
        Then the visitor view "Expensive Mug" variant
        And He should see this variant is discounted from "$50.00" to "$25.00" with "Mug sale" promotion
        And the visitor should see "$2.50" as the price of the "Mug" product in the "Web-US" channel

    @api
    Scenario: Reapplying catalog promotion after editing its rule
        When I edit "Summer sale" catalog promotion to be applied on "Expensive Mug" variant
        And I save my changes
        Then the visitor view "Expensive Mug" variant
        And He should see this variant is discounted from "$50.00" to "$25.00" with "Summer sale" promotion

    @api
    Scenario: Reapplying catalog promotion after removing and adding new rule variant
        When I want to modify a catalog promotion "Summer sale"
        And I remove its every rule
        When I edit "Summer sale" catalog promotion to be applied on "PHP Mug" variant
        And I save my changes
        Then the visitor view "Expensive Mug" variant
        And He should see this variant is not discounted
        And the visitor view "PHP Mug" variant
        And He should see this variant is discounted from "$5.00" to "$2.50" with "Summer sale" promotion
