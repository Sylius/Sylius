@viewing_product_in_admin_panel
Feature: Viewing product's attributes in different locales
    In order to see product's specification in all locales
    As a Administrator
    I want to be able to see product's attributes in all locales

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "Iron Shield"
        And this product has a text attribute "material" with value "oak wood" in "English (United States)" locale
        And this product has a text attribute "material" with value "drewno dębowe" in "Polish (Poland)" locale
        And this product has a textarea attribute "shield details" with value "oak wood is a very good material." in "English (United States)" locale
        And I am logged in as an administrator
        And I am browsing products

    @ui @api
    Scenario: Viewing product's attributes defined in different locales
        When I access the "Iron Shield" product
        Then I should see attribute "material" with value "oak wood" in "English (United States)" locale
        And I should see attribute "shield details" with value "oak wood is a very good material." in "English (United States)" locale
        And I should see attribute "material" with value "drewno dębowe" in "Polish (Poland)" locale
