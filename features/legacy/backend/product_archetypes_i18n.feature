@legacy @i18n
Feature: Product archetype translations
    In order to create similar products faster
    As a store owner
    I want to be able to create archetypes with localised names

    Background:
        Given store has default configuration
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | es_ES |
        And I am logged in as administrator

    Scenario: Creating a product archetype requires default translation fields
        Given I am on the product archetype creation page
        And I fill in the following:
            | Code                                             | Shirt    |
            | sylius_product_archetype_translations_es_ES_name | Camiseta |
        When I press "Create"
        Then I should still be on the product archetype creation page
        And I should see "Please enter archetype name"

    Scenario: Creating a product in archetype specific locale
        Given I am on the product archetype creation page
        And I fill in the following:
            | Code                                             | Shirt    |
            | sylius_product_archetype_translations_es_ES_name | Camiseta |
            | sylius_product_archetype_translations_en_US_name | Shirt    |
        When I press "Create"
        Then "Product archetype has been successfully created" should appear on the page
        And "es_ES" translation for product archetype "Camiseta" should exist
        And "en_US" translation for product archetype "Shirt" should exist
