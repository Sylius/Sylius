@managing_product_variants
Feature: Sorting listed product variants from a product by position
    In order to change the order by which product variants from a product are displayed
    As an Administrator
    I want to sort product variants from a product by their positions

    Background:
        Given the store operates on a single channel in "United States"
        And the store has an "Opel Insignia" configurable product
        And this product has an "Opel Insignia Sports Tourer" variant at position 2
        And this product has also an "Opel Insignia Hatchback" variant at position 0
        And this product has also an "Opel Insignia Sedan" variant at position 1
        And I am logged in as an administrator

    @ui
    Scenario: Product variants are sorted by position in ascending order by default
        When I view all variants of the product "Opel Insignia"
        Then I should see 3 variants in the list
        And the first variant in the list should have name "Opel Insignia Hatchback"
        And the last variant in the list should have name "Opel Insignia Sports Tourer"

    @ui
    Scenario: Sorting product variants in descending order
        When I view all variants of the product "Opel Insignia"
        And I start sorting variants by position
        Then the first variant in the list should have name "Opel Insignia Sports Tourer"
        And the last variant in the list should have name "Opel Insignia Hatchback"

    @ui
    Scenario: New product variant with no position is added as the last one
        Given the product "Opel Insignia" has also an "Opel Insignia Country Tourer" variant
        When I view all variants of the product "Opel Insignia"
        Then I should see 4 variants in the list
        And the last variant in the list should have name "Opel Insignia Country Tourer"

    @ui
    Scenario: New product variant with position 0 is added as the first one
        Given the product "Opel Insignia" has also an "Opel Insignia Country Tourer" variant at position 0
        When I view all variants of the product "Opel Insignia"
        Then I should see 4 variants in the list
        And the first variant in the list should have name "Opel Insignia Country Tourer"

    @ui @javascript
    Scenario: Setting product variant as the first one in the list
        When I view all variants of the product "Opel Insignia"
        And I set the position of "Opel Insignia Sedan" to 0
        And I save my new configuration
        And the first variant in the list should have name "Opel Insignia Sedan"

    @ui @javascript
    Scenario: Setting product variant as the last one in the list
        When I view all variants of the product "Opel Insignia"
        And I set the position of "Opel Insignia Sedan" to 7
        And I save my new configuration
        And the last variant in the list should have name "Opel Insignia Sedan"
