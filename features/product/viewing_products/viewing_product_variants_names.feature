@viewing_products
Feature: Viewing product's variants names
    In order to differentiate product's variants by names
    As a Customer
    I want to be aware of product's variants names
    
    Background:
        Given the store operates on a single channel in "United States"
        And the store is available in "English (United States)"
        And the store is also available in "Polish (Poland)"
        And the store has a "Die Hard Movie" configurable product
        And the it has variant named "Die Hard - Extended Cut" in "English (United States)" and "Szklana Pułapka - Wersja Reżyserska" in "Polish (Poland)"

    @ui @todo
    Scenario: Seeing variant's name in default locale
        When I view product "Wyborowa Vodka"
        Then its current variant should be named "Die Hard - Extended Cut"

    @ui @todo
    Scenario: Seeing proper variant's name after locale change
        When I switch to the "Polish (Poland)" locale
        And I view product "Wyborowa Vodka"
        Then its current variant should be named "Szklana Pułapka - Wersja Reżyserska"
