@i18n
Feature: taxonomies
    In order to improve the store SEO
    As a store owner
    I want to be able to have localised permalinks

    Background:
        Given there is default currency configured
        And   there are following locales configured:
            | code | enabled |
            | en   | yes     |
            | es   | yes     |
        And   there are following taxonomies defined:
            | name     |
            | Category |
        And   taxonomy "Category" has following taxons:
            | Clothing > Shirts > Long Sleeve |
        And   the following taxon translations exist
            | taxon       | name        | locale |
            | Category    | Categoria   | es     |
            | Clothing    | Ropa        | es     |
            | Shirts      | Camisas     | es     |
            | Long Sleeve | Manga Larga | es     |

    Scenario: Creating a taxon generates the proper permalink
        Then  Taxon translation "Long Sleeve" should have permalink "category/clothing/shirts/long-sleeve"
        And   Taxon translation "Manga Larga" should have permalink "categoria/ropa/camisas/manga-larga"

    Scenario: Updating a taxon updates children permalinks only for the given locale
        When  I change then name of taxon translation "Shirts" to "New Shirts"
        Then  Taxon translation "Long Sleeve" should have permalink "category/clothing/new-shirts/long-sleeve"
        And   Taxon translation "Manga Larga" should have permalink "categoria/ropa/camisas/manga-larga"
