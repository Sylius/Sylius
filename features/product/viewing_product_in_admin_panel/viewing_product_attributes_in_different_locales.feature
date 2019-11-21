@viewing_products
Feature: Viewing product's attributes in different locales
    In order to see product's specification in all locales
    As a Administrator
    I want to be able to see product's attributes in all locales

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Iron shield"
        And this product has text attribute "material" with value "oak wood" in "English (United States)" locale
        And this product has text attribute "material" with value "drewno dębowe" in "Polish (Poland)" locale
        And this product has textarea attribute "shield details" with value "oak wood is a very good material." in "English (United States)" locale
        And I am logged in as an administrator
        And I am browsing products

    @ui
    Scenario: Viewing product's attributes defined in different locales
        When I access "Iron Shield" product page
        Then I should see attribute "material" with value "oak wood" in "English (United States)" locale
        And I should see attribute "shield details" with value "oak wood is a very good material." in "English (United States)" locale
        And I should see attribute "material" with value "drewno dębowe" in "Polish (Poland)" locale
