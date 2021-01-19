<?php

namespace Buoy\LighthouseFairway\Schema\Directives;

use GraphQL\Language\AST\TypeDefinitionNode;
use GraphQL\Language\Parser;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Schema\AST\DocumentAST;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use Nuwave\Lighthouse\Support\Contracts\TypeManipulator;

class SubscribableDirective extends BaseDirective implements TypeManipulator
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'SDL'
"""
Make a field subscribable. 
"""
directive @subscribable on OBJECT
SDL;
    }

    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition): void
    {
        $model = $typeDefinition->name->value;

        $documentAST->types["{$model}Event"] = Parser::objectTypeDefinition(/** @lang GraphQL */"
            \"The type of model event.\"
            type {$model}Event {
                id: ID!
                event: EventType!
                model: {$model}
            }
        ");

        $subscription = Str::camel(class_basename($model)) . 'Modified';

        $documentAST->types["Subscription"] = Parser::objectTypeDefinition(/** @lang GraphQL */"
            type Subscription {
                {$subscription}(events: [EventType!], id: ID): {$model}Event
                @subscription(class: \"\\\\Buoy\\\\LighthouseFairway\\\\Subscriptions\\\\ModelEventSubscription\")
            }
        ");
    }
}
