@managing_product_variants
Feature: Editing a product variant
    In order to change product variant details
    As an Administrator
    I want to be able to edit a product variant

    Background:
        Given the store operates on a single channel in "United States"
        And this channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a "T-Shirt" configurable product
        And this product has "Go" variant priced at "$100.00" in "United States" channel
        And this product is named "Go" in the "English (United States)" locale
        And this product is named "Idź" in the "Polish (Poland)" locale
        And I am logged in as an administrator

    @api @todo @ui
    Scenario: Changing product variant name
        Given I want to modify the "Go" product variant
        And I name it "Java" in "English (United States)"
        And I name it "Kawa" in "Polish (Poland)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the variant with code "GO" should be named "Java" in "English (United States)" locale
        And the variant with code "GO" should be named "Kawa" in "Polish (Poland)" locale
