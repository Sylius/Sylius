@viewing_products
Feature: Seeing the corresponding lowest price before the discount when selecting different product's options
    In order to be aware of the lowest price before the discount for the chosen product option
    As a Customer
    I want to see the corresponding lowest price before the discount for the chosen product option

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Exquisite", "Lemon" and "Bitter"
        And this product has "Wyborowa-Vodka-1" variant priced at "$20.00" configured with "EXQUISITE" option value
        And this product has "Wyborowa-Vodka-2" variant priced at "$25.00" configured with "LEMON" option value
        And this product has "Wyborowa-Vodka-3" variant priced at "$35.00" configured with "BITTER" option value
        And this product is configured with the option matching selection method
        And the store has a "Bocian Vodka" configurable product
        And this product has option "Size" with values "Small" and "Medium"
        And this product has option "Color" with values "Blue" and "Green"
        And this product has all possible variants priced at "$10.00" with indexed names
        And this product is configured with the option matching selection method
        And there is a catalog promotion "Winter sale" with priority 1 that reduces price by "50%" and applies on "Wyborowa Vodka" product
        And there is a catalog promotion "Summer sale" with priority 1 that reduces price by "50%" and applies on "Bocian Vodka variant 0" variant


    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting first option value from the list
        When I view product "Wyborowa Vodka"
        And I select its "Taste" as "Exquisite"
        Then I should see "$20.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting another option value from the list
        When I view product "Wyborowa Vodka"
        And I select its "Taste" as "Lemon"
        Then I should see "$25.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when selecting last option value from the list after selecting another option value from the list
        When I view product "Wyborowa Vodka"
        And I select its "Taste" as "Lemon"
        And I select its "Taste" as "Exquisite"
        And I select its "Taste" as "Bitter"
        Then I should see "$35.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Seeing correct lowest price when having discounted variant with more than one option value
        When I view product "Bocian Vodka"
        And I select its "Color" as "Blue"
        And I select its "Size" as "Small"
        Then I should see "$10.00" as its lowest price before the discount

    @no-api @ui @javascript
    Scenario: Not seeing the lowest price when having variant with more than one option value and without discount
        When I view product "Bocian Vodka"
        And I select its "Color" as "Blue"
        And I select its "Size" as "Medium"
        Then I should not see information about its lowest price
