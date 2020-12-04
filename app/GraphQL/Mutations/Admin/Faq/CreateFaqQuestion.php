<?php

namespace App\GraphQL\Mutations\Admin\Faq;

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\AdminPermissionsHelper;
use App\Http\Requests\Admin\Faq\CreateFaqQuestionRequest;
use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use App\Traits\DynamicValidation;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Support\Arr;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class CreateFaqQuestion
{
    use DynamicValidation;

    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  CreateFaqQuestionRequest  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     * @throws GraphQLSaveDataException
     */
    protected function resolve($rootValue, CreateFaqQuestionRequest $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $user = $context->user();

        AdminPermissionsHelper::check('faq', $user);

        $inputs = $args->validated();

        $category_id = Arr::get($inputs, 'category_id');

        $category = FaqCategory
            ::whereId($category_id)
            ->firstOrFail();

        $question = new FaqQuestion();

        $question->locale = $category->locale;
        $question->category_id = Arr::pull($inputs, 'category_id');

        $question->fill($inputs);

        if (!$question->save()) {
            throw new GraphQLSaveDataException(__('faq.question_create_failed'), __('Error'));
        }

        return $question;
    }
}
