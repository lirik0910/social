<?php

namespace App\GraphQL\Unions;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Nuwave\Lighthouse\Schema\TypeRegistry;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Pub
{
    /**
     * The type registry.
     *
     * @var \Nuwave\Lighthouse\Schema\TypeRegistry
     */
    protected $typeRegistry;

    /**
     * Constructor.
     *
     * @param  \Nuwave\Lighthouse\Schema\TypeRegistry  $typeRegistry
     * @return void
     */
    public function __construct(TypeRegistry $typeRegistry)
    {
        $this->typeRegistry = $typeRegistry;
    }

    /**
     * Decide which GraphQL type a resolved value has.
     *
     * @param mixed $rootValue The value that was resolved by the field. Usually an Eloquent model.
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext $context
     * @param \GraphQL\Type\Definition\ResolveInfo $resolveInfo
     * @return \GraphQL\Type\Definition\Type
     * @throws \Nuwave\Lighthouse\Exceptions\DefinitionException
     */
    public function __invoke($rootValue, GraphQLContext $context, ResolveInfo $resolveInfo): Type
    {
        $class_basename = class_basename($rootValue);

        switch ($class_basename) {
            case 'Auction':
                $name = 'GuestAuction';
                break;
            case 'Advert':
                $name = 'GuestAdvert';
                break;
            default:
                $name = $class_basename;
        }

        return $this->typeRegistry->get($name);
    }
}
