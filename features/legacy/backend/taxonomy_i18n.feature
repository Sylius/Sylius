@legacy @i18n
Feature: Taxons internationalization
    In order to improve the store SEO
    As a store owner
    I want to be able to have localised permalinks

    Background:
        Given store has default configuration
        And there are following locales configured and assigned to the default channel:
            | code  |
            | en_US |
            | es_ES |
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
            | Clothing[TX1] > Shirts[TX2] > Long Sleeve[TX3] |
        And the following taxon translations exist:
            | taxon       | name        | locale |
            | Category    | Categoria   | es_ES  |
            | Clothing    | Ropa        | es_ES  |
            | Shirts      | Camisas     | es_ES  |
            | Long Sleeve | Manga Larga | es_ES  |

    Scenario: Creating a taxon generates the proper permalink
        Then taxon translation "Long Sleeve" should have permalink "category/clothing/shirts/long-sleeve"
        And taxon translation "Manga Larga" should have permalink "categoria/ropa/camisas/manga-larga"

    Scenario: Updating a taxon updates children permalinks only for the given locale
        When I change then name of taxon translation "Shirts" to "New Shirts"
        Then taxon translation "Long Sleeve" should have permalink "category/clothing/new-shirts/long-sleeve"
        And taxon translation "Manga Larga" should have permalink "categoria/ropa/camisas/manga-larga"
