<?php

namespace Buoy\LighthouseFairway\Schema\Directives;

use Buoy\LighthouseFairway\Exceptions\NoSubscriptionException;
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
directive @subscribable (
    "Use a custom class for the subscription"
    class: String
) on OBJECT
SDL;
    }

    /**
     * @throws NoSubscriptionException
     */
    public function manipulateTypeDefinition(DocumentAST &$documentAST, TypeDefinitionNode &$typeDefinition): void
    {
        $model = $typeDefinition->name->value;
        $directive = collect($typeDefinition->directives)->where('name.value', '=', 'subscribable')->first();
        $arguments = collect($directive->arguments);
        $subscriptionClass = optional(optional($arguments->where('name.value', '=', 'class')->first())->value)->value;

        $documentAST->types["{$model}Event"] = Parser::objectTypeDefinition(/** @lang GraphQL */"
            \"The type of model event.\"
            type {$model}Event {
                id: ID!
                event: EventType!
                model: {$model}
            }
        ");

        $subscription = Str::camel(class_basename($model)) . 'Modified';

        if (!$subscriptionClass) {
            $subscriptionClass = '\\' . config('lighthouse-fairway.subscription_class');
            if (!class_exists($subscriptionClass)) {
                throw new NoSubscriptionException('"subscription_class" in the lighthouse-fairway config must be a valid namespace.');
            }
            $subscriptionClass = str_replace('\\', '\\\\', $subscriptionClass);
        }

        $definition = Parser::objectTypeDefinition(/** @lang GraphQL */"
            type Subscription {
                {$subscription}(events: [EventType!], id: ID): {$model}Event
                @subscription(class: \"{$subscriptionClass}\")
            }
        ");

        // Merge the field into the Subscription type
        if ($documentAST->types["Subscription"]) {
            $documentAST->types["Subscription"]->fields = $documentAST->types["Subscription"]->fields
                ->merge($definition->fields);
        } else {
            $documentAST->types["Subscription"] = $definition;
        }
    }
}
