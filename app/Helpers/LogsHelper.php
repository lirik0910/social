<?php


namespace App\Helpers;


use App\Exceptions\GraphQLSaveDataException;
use App\GraphQL\Mutations\Admin\CharityOrganization\ApproveCustomCharityOrganization;
use App\GraphQL\Mutations\Admin\CharityOrganization\CreateCharityOrganization;
use App\GraphQL\Mutations\Admin\CharityOrganization\DeleteNativeCharityOrganization;
use App\GraphQL\Mutations\Admin\CharityOrganization\UpdateNativeCharityOrganization;
use App\GraphQL\Mutations\Admin\Media\AcceptVerifying;
use App\GraphQL\Mutations\Admin\Media\BlockMedia;
use App\GraphQL\Mutations\Admin\Media\RejectVerifying;
use App\GraphQL\Mutations\Admin\PaymentPercent\CreatePaymentPercent;
use App\GraphQL\Mutations\Admin\PaymentPercent\DeletePaymentPercent;
use App\GraphQL\Mutations\Admin\PaymentPercent\EditPaymentPercent;
use App\GraphQL\Mutations\Admin\PaymentTransaction\ChangeUserBalance;
use App\GraphQL\Mutations\Admin\Present\CreatePresentCategory;
use App\GraphQL\Mutations\Admin\Present\CreatePresents;
use App\GraphQL\Mutations\Admin\Present\DeletePresent;
use App\GraphQL\Mutations\Admin\Present\DeletePresentCategory;
use App\GraphQL\Mutations\Admin\Present\UpdatePresent;
use App\GraphQL\Mutations\Admin\Present\UpdatePresentCategory;
use App\GraphQL\Mutations\Admin\ProfilesBackground\DeleteProfilesBackground;
use App\GraphQL\Mutations\Admin\ProfilesBackground\UploadProfilesBackgrounds;
use App\GraphQL\Mutations\Admin\ProfilesBackground\UpdateProfilesBackground;
use App\GraphQL\Mutations\Admin\Report\ApproveReport;
use App\GraphQL\Mutations\Admin\Report\DeclineReport;
use App\GraphQL\Mutations\Admin\User\BanUser;
use App\GraphQL\Mutations\Admin\User\UnbanUser;
use App\GraphQL\Mutations\Admin\VerificationSign\DeleteVerificationSign;
use App\GraphQL\Mutations\Admin\VerificationSign\UploadVerificationSigns;
use App\GraphQL\Mutations\Admin\VerificationSign\UpdateVerificationSign;
use App\Models\GlobalLog;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;


class LogsHelper
{
    /**
     * @param $user
     * @param array $data
     * @throws GraphQLSaveDataException
     */
    public static function createLog($user, array $data)
    {
        $parsed_operation_data = self::parseOperationData($data);

        if (!empty($parsed_operation_data)) {
            $mutation_data = self::getMutationData($parsed_operation_data['name']);

            if (!empty($mutation_data)) {
                $mutation_key = array_key_first($mutation_data);

                $log = new GlobalLog();

                $log->fill([
                    'mutation' => $mutation_key,
                    'section' => $mutation_data[$mutation_key]['section'],
                    'data' => $parsed_operation_data['data'],
                    'user_id' => $user->id,
                    'user_nickname' => $user->nickname,
                ]);

                if (!$log->save()) {
                    throw new GraphQLSaveDataException(__('admin.cannot_save_log'), __('Error'));
                }
            }
        }
    }

    /**
     * Return parsed mutation request data
     *
     * @param array $data
     * @return array|void
     */
    protected static function parseOperationData(array $data)
    {
        $query_info_field = isset($data['query'])
            ? $data['query']
            : $data['operations'];

        $cnt = preg_match('~(.*?)mutation(.*?)~', $query_info_field, $matches);

        if($cnt > 0) {
            $str = strtr($query_info_field, ['\\n' => '', '"' => '']);
            $str = preg_replace('~\s*~', '', $str);
            $str = preg_replace('~mutation\{~', '', $str) . '}';
            $str = preg_replace('~\}\).*~', '', $str);

            $mutationArray = explode("(", $str);

            $mutationData = preg_replace('~\.*data:\{~', '', $mutationArray[1]) . ',';
            $mutationData = '{' . substr(preg_replace('~(.*?):(.*?),~', '"$1":"$2",', $mutationData), 0, -1) . '}';

            return [
                'name' => $mutationArray[0],
                'data' => $mutationData,
            ];
        }
    }

    /**
     * Return mutation data from existing structure
     *
     * @param string $name
     * @return array|array[]
     */
    protected static function getMutationData(string $name)
    {
        $exists_mutations = self::getExistingMutationsDataForLog();

        return Arr::where($exists_mutations, function ($value) use ($name) {
            return Str::afterLast($value['mutation'], '\\') === ucfirst($name);
        });
    }

    /**
     * Return data for settings
     *
     * @return array
     */
    public static function getLogsSettings()
    {
        $logs_data = self::getExistingMutationsDataForLog();

        $settings = [];

        foreach ($logs_data as $value => $arr) {
            $settings[$value] = isset($arr['name']) && !empty($arr['name'])
                ? $arr['name']
                : lcfirst(Str::afterLast($arr['mutation'], '\\'));
        }

        return $settings;
    }

    /**
     * Return mutations data for logs
     *
     * @return array[]
     */
    protected static function getExistingMutationsDataForLog()
    {
        return [
            1 => [
                'mutation' => CreateCharityOrganization::class,
                'name' => __('logs.charity_create'),
                'section' => GlobalLog::ADMIN_SECTION_CHARITY
            ],
            2 => [
                'mutation' => UpdateNativeCharityOrganization::class,
                'name' => __('logs.charity_update'),
                'section' => GlobalLog::ADMIN_SECTION_CHARITY
            ],
            3 => [
                'mutation' => DeleteNativeCharityOrganization::class,
                'name' => __('logs.charity_delete'),
                'section' => GlobalLog::ADMIN_SECTION_CHARITY
            ],
            4 => [
                'mutation' => ApproveCustomCharityOrganization::class,
                'name' => __('logs.custom_charity_approve'),
                'section' => GlobalLog::ADMIN_SECTION_CHARITY
            ],
            5 => [
                'mutation' => DeleteNativeCharityOrganization::class,
                'name' => __('logs.custom_charity_decline'),
                'section' => GlobalLog::ADMIN_SECTION_CHARITY
            ],
            6 => [
                'mutation' => BlockMedia::class,
                'name' => __('logs.media_block'),
                'section' => GlobalLog::ADMIN_SECTION_MEDIA
            ],
            7 => [
                'mutation' => AcceptVerifying::class,
                'name' => __('logs.verification_request_accept'),
                'section' => GlobalLog::ADMIN_SECTION_VERIFICATION
            ],
            8 => [
                'mutation' => RejectVerifying::class,
                'name' => __('logs.verification_request_reject'),
                'section' => GlobalLog::ADMIN_SECTION_VERIFICATION
            ],
            9 => [
                'mutation' => UploadVerificationSigns::class,
                'name' => __('logs.verification_sign_create'),
                'section' => GlobalLog::ADMIN_SECTION_VERIFICATION
            ],
            10 => [
                'mutation' => UpdateVerificationSign::class,
                'name' => __('logs.verification_sign_update'),
                'section' => GlobalLog::ADMIN_SECTION_VERIFICATION
            ],
            11 => [
                'mutation' => DeleteVerificationSign::class,
                'name' => __('logs.verification_sign_delete'),
                'section' => GlobalLog::ADMIN_SECTION_VERIFICATION
            ],
            12 => [
                'mutation' => CreatePaymentPercent::class,
                'name' => __('logs.payment_percent_create'),
                'section' => GlobalLog::ADMIN_SECTION_PAYMENT
            ],
            13 => [
                'mutation' => EditPaymentPercent::class,
                'name' => __('logs.payment_percent_edit'),
                'section' => GlobalLog::ADMIN_SECTION_PAYMENT
            ],
            14 => [
                'mutation' => DeletePaymentPercent::class,
                'name' => __('logs.payment_percent_delete'),
                'section' => GlobalLog::ADMIN_SECTION_PAYMENT
            ],
            15 => [
                'mutation' => ChangeUserBalance::class,
                'name' => __('logs.user_balance_changed'),
                'section' => GlobalLog::ADMIN_SECTION_PAYMENT
            ],
            16 => [
                'mutation' => UploadProfilesBackgrounds::class,
                'name' => __('logs.profiles_background_create'),
                'section' => GlobalLog::ADMIN_SECTION_MEDIA
            ],
            17 => [
                'mutation' => UpdateProfilesBackground::class,
                'name' => __('logs.profiles_background_update'),
                'section' => GlobalLog::ADMIN_SECTION_MEDIA
            ],
            18 => [
                'mutation' => DeleteProfilesBackground::class,
                'name' => __('logs.profiles_background_delete'),
                'section' => GlobalLog::ADMIN_SECTION_MEDIA
            ],
            19 => [
                'mutation' => ApproveReport::class,
                'name' => __('logs.report_approve'),
                'section' => GlobalLog::ADMIN_SECTION_REPORTS
            ],
            20 => [
                'mutation' => DeclineReport::class,
                'name' => __('logs.report_decline'),
                'section' => GlobalLog::ADMIN_SECTION_REPORTS
            ],
            21 => [
                'mutation' => BanUser::class,
                'name' => __('logs.user_ban'),
                'section' => GlobalLog::ADMIN_SECTION_USERS
            ],
            22 => [
                'mutation' => UnbanUser::class,
                'name' => __('logs.user_unban'),
                'section' => GlobalLog::ADMIN_SECTION_USERS
            ],
            23 => [
                'mutation' => CreatePresentCategory::class,
                'name' => __('logs.presents_category_create'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
            24 => [
                'mutation' => UpdatePresentCategory::class,
                'name' => __('logs.presents_category_update'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
            25 => [
                'mutation' => DeletePresentCategory::class,
                'name' => __('logs.presents_category_delete'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
            26 => [
                'mutation' => CreatePresents::class,
                'name' => __('logs.present_create'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
            27 => [
                'mutation' => UpdatePresent::class,
                'name' => __('logs.present_update'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
            28 => [
                'mutation' => DeletePresent::class,
                'name' => __('logs.present_delete'),
                'section' => GlobalLog::ADMIN_SECTION_PRESENTS
            ],
        ];
    }
}
