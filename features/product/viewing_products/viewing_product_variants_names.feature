@viewing_products
Feature: Viewing product's variants names
    In order to differentiate product's variants by names
    As a Customer
    I want to be aware of product's variants names

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And the store has a "Die Hard Movie" configurable product
        And it has variant named "Die Hard - Extended Cut" in "English (United States)" locale and "Szklana Pułapka - Wersja Reżyserska" in "Polish (Poland)" locale
        And this product has also variant named "Die Hard - Theatrical Cut" in "English (United States)" locale and "Szklana Pułapka - Wersja Kinowa" in "Polish (Poland)" locale

    @ui
    Scenario: Seeing variant's name in default locale
        When I view product "Die Hard Movie"
        Then its current variant should be named "Die Hard - Extended Cut"

    @ui
    Scenario: Seeing proper variant's name after locale change
        When I view product "Die Hard Movie" in the "Polish (Poland)" locale
        Then its current variant should be named "Szklana Pułapka - Wersja Reżyserska"
