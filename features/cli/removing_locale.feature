@removing_locale
Feature: Removing locales
    In order to deleting accidentally created locales
    As a Developer
    I want to have a way to remove a given locale

    Background:
        Given the store operates on a channel named "Web" with hostname "web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default

    @cli
    Scenario: The developer tries to delete a not used locale
        Given the store has a product "T-Shirt banana"
        And this product is named "Banana T-Shirt with Minions" in the "English (United States)" locale
        And this product has no translation in the "Polish (Poland)" locale
        When I run a command removing the "Polish (Poland)" locale
        Then I should be informed that locale "Polish (Poland)" has been deleted
        And only the "English (United States)" locale should be present in the system

    @cli
    Scenario: The developer tries to deleted a locale in usage
        Given the store has a product "T-Shirt banana"
        And this product is named "Banana T-Shirt with Minions" in the "English (United States)" locale
        And this product is named "Koszulka Banan z Minionami" in the "Polish (Poland)" locale
        When I run a command removing the "Polish (Poland)" locale
        Then I should be informed that locale "Polish (Poland)" is in use and cannot be deleted
        And the "Polish (Poland)" locale should be still present in the system
