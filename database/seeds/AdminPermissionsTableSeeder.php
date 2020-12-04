<?php

use Illuminate\Database\Seeder;

class AdminPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('admin_permissions')->delete();

        $permissions = [
            [
                'title' => 'Service pages',
                'action' => 'service_pages',
            ],
            [
                'title' => 'Meeting information',
                'action' => 'meeting_info',
            ],
            [
                'title' => 'Auction information',
                'action' => 'auction_info',
            ],
            [
                'title' => 'Advert information',
                'action' => 'advert_info',
            ],
            [
                'title' => 'Admin logs',
                'action' => 'logs',
            ],
            [
                'title' => 'Ban media',
                'action' => 'media_ban',
            ],
            [
                'title' => 'Payment percent',
                'action' => 'payment_percent',
            ],
            [
                'title' => 'Profile backgrounds',
                'action' => 'background',
            ],
            [
                'title' => 'Presents',
                'action' => 'present',
            ],
            [
                'title' => 'Charity organizations',
                'action' => 'charity',
            ],
            [
                'title' => 'FAQ',
                'action' => 'faq',
            ],
            [
                'title' => 'User verification',
                'action' => 'verification',
            ],
            [
                'title' => 'User ban',
                'action' => 'user_ban',
            ],
            [
                'title' => 'Change user balance',
                'action' => 'user_balance_change',
            ],
            [
                'title' => 'Support payment',
                'action' => 'support_payment',
            ],
            [
                'title' => 'Support meeting',
                'action' => 'support_meeting',
            ],
            [
                'title' => 'Support_advert',
                'action' => 'support_advert',
            ],
            [
                'title' => 'Support auction',
                'action' => 'support_auction',
            ],
            [
                'title' => 'Support upload',
                'action' => 'support_upload',
            ],
            [
                'title' => 'Support feedback',
                'action' => 'support_feedback',
            ],
            [
                'title' => 'Support login',
                'action' => 'support_login',
            ],
            [
                'title' => 'Support account access',
                'action' => 'support_account_access',
            ],
            [
                'title' => 'Support avatar verification',
                'action' => 'support_avatar_verification',
            ],
            [
                'title' => 'Support charity verification',
                'action' => 'support_charity_verification',
            ],
            [
                'title' => 'User reports',
                'action' => 'report_user',
            ],
            [
                'title' => 'Media reports',
                'action' => 'report_media',
            ],
            [
                'title' => 'Meeting reports',
                'action' => 'report_meeting',
            ],
            [
                'title' => 'Auction reports',
                'action' => 'report_auction',
            ],
            [
                'title' => 'Advert reports',
                'action' => 'report_advert',
            ],
            [
                'title' => 'Chat room reports',
                'action' => 'report_chat_room',
            ],
        ];

        foreach ($permissions as $permission) {
            \App\Models\AdminPermission::create($permission);
        }
    }
}
