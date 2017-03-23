@managing_product_variants
Feature: Adding a new product variant
    In order to sell different variations of a single product
    As an Administrator
    I want to add a new product variant to the shop

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And the store has "Fragile" shipping category
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new product variant
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant with name
        Given the store is also available in "Polish (Poland)"
        And I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I name it "Vodka Wyborowa Premium" in "English (United States)"
        And I name it "Wódka Wyborowa Premium" in "Polish (Poland)"
        And I set its price to "$100.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be named "Vodka Wyborowa Premium" in "English (United States)" locale
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be named "Wódka Wyborowa Premium" in "Polish (Poland)" locale

    @ui
    Scenario: Adding a new product variant with specific option's value
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_MELON"
        And I set its price to "$80.00" for "United States" channel
        And I set its "Taste" option to "Melon"
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_MELON" variant of the "Wyborowa Vodka" product should appear in the store

    @ui
    Scenario: Adding a new product variant with specific shipping category
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I set its shipping category as "Fragile"
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store

    @ui
    Scenario: Adding a new product variant with discounted price
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_EXQUISITE"
        And I set its price to "$100.00" for "United States" channel
        And I set its original price to "$120.00" for "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "VODKA_WYBOROWA_EXQUISITE" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_EXQUISITE" should be priced at $100.00 for channel "United States"
        And the variant with code "VODKA_WYBOROWA_EXQUISITE" should have an original price of $120.00 for channel "United States"

    @ui
    Scenario: Adding a new product variant without shipping required
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its price to "$100.00" for "United States" channel
        And I do not want to have shipping required for this product
        And I add it
        Then I should be notified that it has been successfully created
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should not have shipping required
        And the "VODKA_WYBOROWA_PREMIUM" variant of the "Wyborowa Vodka" product should appear in the store
        And the variant with code "VODKA_WYBOROWA_PREMIUM" should be priced at $100.00 for channel "United States"