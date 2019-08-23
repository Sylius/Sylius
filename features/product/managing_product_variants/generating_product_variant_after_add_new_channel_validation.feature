@managing_product_variants
Feature: Generating product variant after add new channel
    In order to avoid making mistakes when generating variants
    As an Administrator
    I want to be prevented from generating it without specifying required fields

    Background:
        Given the store operates on a channel named "United States"
        And the store also operates on a channel named "Europe"
        And the store has a "Wyborowa Vodka" configurable product
        And this product has option "Taste" with values "Orange" and "Melon"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new product variant without specifying its price in new added channel
        Given this product has "Wyborowa Exquisite" variant priced at "$90" identified by "VODKA_WYBOROWA_PREMIUM"
        And I want to generate new variants for this product
        And I try to generate it
        And I should be notified that prices in all channels must be defined for the 1st variant
