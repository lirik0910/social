<?php


namespace App\GraphQL\ResolversOld\CharityOrganization;


use App\Models\User;
use App\Exceptions\GraphQLSaveDataException;
use App\Models\CharityOrganization;
use App\Traits\RequestDataValidate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException as GraphQLValidationException;

class CharityOrganizationResolver
{
    use RequestDataValidate;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create/Update native charity organization
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return object
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveUpdateNative($rootValue, array $args)
    {
        $this->user = Auth::user();

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        if(!empty($inputs['id'])){
            $charity_organization = CharityOrganization::whereId($inputs['id'])->firstOrFail();
        } else {
            $charity_organization = new CharityOrganization();
        }

        $charity_organization->fill($inputs);

        if (!$charity_organization->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.update_failed'), __('Error'));
        }

        return $charity_organization;
    }

    /**
     * Create/Update custom charity organization
     *
     * @param       $rootValue
     * @param array $args
     *
     * @return object
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Joselfonseca\LighthouseGraphQLPassport\Exceptions\ValidationException
     * @throws \App\Exceptions\GraphQLSaveDataException
     */
    public function resolveUpdateCustom($rootValue, array $args)
    {
        $this->user = Auth::user();

        try {
            $inputs = $this->validatedData($args['data']);
        } catch (ValidationException $e) {
            throw new GraphQLValidationException($e->errors(), __('Input validation failed.'));
        }

        if($this->user->charity_organization){
            $charity_organization = $this->user->charity_organization;
        } else {
            $charity_organization = new CharityOrganization();
            $charity_organization->user_id = $this->user->id;
        }

        $charity_organization->fill($inputs);

        if (!$charity_organization->save()) {
            throw new GraphQLSaveDataException(__('charity_organization.update_failed'), __('Error'));
        }

        return $charity_organization;
    }

    /**
     * List of validation rules
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'name' => 'required|string',
        ];

        if($this->user->role !== User::ROLE_USER) {
            $rules += [
                'id' => 'sometimes|integer',
                'image' => 'nullable|string',
                'image_url' => 'nullable|string',
                'description' => 'required|string',
                'link' => 'nullable|url',
            ];
        }

        return $rules;
    }
}
