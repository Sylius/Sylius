@managing_product_variants
Feature: Browsing product variants in different locales
    In order to see all product variants
    As an Administrator
    I want to browse product variants of specific product in different locales

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using "English (United States)", "Polish (Poland)" and "Irish (Ireland)" locales
        And the store has a "Berserk Pug" configurable product
        And this product has variant named "Berserk Pug with Axes" in "English (United States)" locale and "Szałowy Mops z Toporami" in "Polish (Poland)" locale
        And this product also has a variant named "Berserk Pug with Morning Star" in "English (United States)" locale
        And this variant has no translation in "Polish (Poland)" locale
        And this product also has a variant named "Szałowy Mops z Mieczem" in "Polish (Poland)" locale
        And this variant has no translation in "English (United States)" locale
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Seeing all variants when some don't have translations in my locale which is also the base
        When I want to view all variants of this product
        Then I should see 3 variants in the list
        And I should see a variant named "Berserk Pug with Axes"
        And I should also see a variant named "Berserk Pug with Morning Star"
        And I should also see 1 variant with no name

    @ui @no-api
    Scenario: Seeing all variants when some don't have translations in my locale which is not the base
        Given I change my locale to "Polish (Poland)"
        When I want to view all variants of this product
        Then I should see 3 variants in the list
        And I should see a variant named "Szałowy Mops z Toporami"
        And I should also see a variant named "Szałowy Mops z Mieczem"
        And I should also see 1 variant with no name

    @ui @no-api
    Scenario: Seeing all variants when none have names in my locale
        Given I change my locale to "Irish (Ireland)"
        When I want to view all variants of this product
        Then I should see 3 variants in the list
        And I should see 3 variants with no name
