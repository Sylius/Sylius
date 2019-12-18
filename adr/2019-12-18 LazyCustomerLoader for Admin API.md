Date: 2019-12-18

# LazyCustomerLoader for Admin API

In power from version: 1.5.9, 1.6.5, 1.7.0

Related PRs: #9050 

Appends ADRs: 

## Problem to solve 
Loading time of order creation via Admin API grows significantly with high amount of customers (reported amount - 30k). Problem occurs due to usage of \Sylius\Bundle\CustomerBundle\Form\Type\CustomerChoiceType, where all customer entities are loaded to form type, just in order to set one of them.

## Possible solutions
 - Create autocomplete choice type, which will load customers by partial data.
 - Skip loading of all customers to load time and load only chosen option after form submit.

## Context:
 - The #9050 with possible solution was submitted in Dec 2017. This PR contains the solution of skipping loading of all customers. 
 - List of all customers in choice type is not needed in API context, as this form is never rendered. 
 - CustomerChoiceType is only used in Admin API Context.

## Chosen solution and reasoning

Skip loading of all customers to load time and load only chosen option after form submit. 

PR with this solution was already prepared and provide 60% decrease of execution time already(for the set of 10k customers).  What is more, this solution was simpler and easier to maintain (as only one service was introduced). The trade off of current implementation is that \Sylius\Bundle\AdminApiBundle\Form\ChoiceList\Loader\LazyCustomerLoader is not suitable to handle any other request outside of api context. 

Autocomplete seemed to be more advanced solution, but we wouldn't take any advantage of it, because this feature itself wouldn't be used at all. 

LazyLoaders should be considered as a default approach for future, unless Autocomplete feature is used in normal Admin Panel as well.
