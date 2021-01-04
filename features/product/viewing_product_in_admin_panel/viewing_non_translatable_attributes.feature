@viewing_products
Feature: Viewing product's non translatable attributes
    In order to see product's non translatable attribute
    As a Administrator
    I want to be able to see product's single non translatable attribute

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Iron Pickaxe"
        And this product has non-translatable percent attribute "crit chance" with value 10%
        And I am logged in as an administrator
        And I am browsing products

    @ui
    Scenario: Viewing product's attributes defined in different locales
        When I access "Iron Pickaxe" product page
        And I should see attribute "crit chance" with value "10%" in "Polish (Poland)" locale
        And I should see non
