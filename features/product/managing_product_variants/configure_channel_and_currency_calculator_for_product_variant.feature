@managing_product_variants
Feature: Configure channel and currency calculator
    In order to define products price per channel and currency
    As an Administrator
    I want to add a new product to the shop with different prices

    Background:
        Given the store is available in "English (United States)"
        And the store operates on a channel named "Web"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Configure channel and currency calculator while adding new variant
        Given I want to create a new variant of this product
        When I specify its code as "VODKA_WYBOROWA_PREMIUM"
        And I set its default price to "$100.00"
        And I choose "Channel and currency" calculator
        And I set its price to "$10.00" for "USD" currency and "Web" channel
        And I add it
        Then I should be notified that it has been successfully created
        And variant with code "VODKA_WYBOROWA_PREMIUM" for "USD" currency and "Web" channel should have "$10.00"
