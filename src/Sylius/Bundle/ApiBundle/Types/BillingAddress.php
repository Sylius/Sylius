<?php


namespace Sylius\Bundle\ApiBundle\Types;


use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class BillingAddress extends InputObjectType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    public function __construct()
    {
        $this->name = 'BillingAddress';
        $this->description = 'The `BillingAddress` object type.';
        $config = [
            'fields' => [
                'id' => Type::id(),
                'firstName'=> Type::string(),
                'lastName'=> Type::string(),
                'countryCode'=> Type::string(),
                'street'=> Type::string(),
                'city'=> Type::string(),
                'postcode'=> Type::string()
            ]
        ];
        parent::__construct($config);
    }
}

