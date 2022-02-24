# Using cart by guest and logged in customer

* Status: accepted
* Date: 2022-02-22

## Context and Problem Statement

Cart and its processing is one of the key aspects of Sylius. It turned out that it has a vulnerability and there is
possible for an anonymous user to override the cart of logged in customer by using only its email. This is because 
when entering an email, during addressing step, the customer with this email is assigned to the cart and from then, 
there is no simple way to distinguish between the carts created by the guest and the logged in user. The question is 
how should we distinguish the carts to solve the vulnerability.

## Decision Drivers

Provided solution should:
* solve the initial problem with overriding cart of logged in customer by anonymous user
* not break a backward compatibility, both code and business behaviour

## Considered Options

### Forcing logging in during checkout

* Good, because it solves the initial problem
* Bad, because it changes the current expected behaviour in checkout
* Bad, because it breaks a backward compatibility from the business perspective

### Changing priority of cart contexts

* Good, because it solves the initial problem
* Bad, because it does not solve the case when a logged in customer does not have a cart
* Bad, because it changes the current expected behaviour with keeping cart after logging in
* Bad, because it breaks a backward compatibility from the business perspective

### Introducing flag on order entity to mark order created by guest

Adding a flag to order entity allows us to distinguish carts created by guest or by logged in customer. To mark cart 
as soon as possible, to solve all cases of the initial problem, the flag needs to be set in Sylius\Component\Core\Cart\Context\ShopBasedCartContext, 
where the customer of logged in user is also set on cart. This context is definitely not a proper service to do such operations, 
however it is consistent with current approach in Sylius. Setting this value only after logging in would not be enough, 
as it doesn't resolve the situation when the already logged in user creates a cart. It is similar with setting flag after
the addressing step, it doesn't resolve the problem as the cart of logged in user is marked too late and before addressing
the cart couldn't be distinguished to whom it belongs.

* Good, because it solves the initial problem
* Good, because it does not break a backward compatibility
* Good, because it can be used to additional features in the future
* Bad, because it requires to set the flag in Sylius\Component\Core\Cart\Context\ShopBasedCartContext 

## Decision Outcome

Chosen option: **"Introducing flag on order entity to mark order created by guest"**

For now, it is the most straightforward solution, that resolves the initial problem, does not introduce any BC break
and it is the solution that could be used to additional features in the future.

## References

* [Approach with changing priorities](https://github.com/Sylius/Sylius/pull/13603)
* [Chosen approach with introducing flag](https://github.com/Sylius/Sylius/pull/13676)
