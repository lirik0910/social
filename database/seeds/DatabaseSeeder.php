<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(PublicStreamSeeder::class);
        //$this->call(MediaTableSeeder::class);
        //$this->call(AuctionTableSeeder::class);
        //$this->call(AuctionBidsTableSeeder::class);
        //$this->call(PublicStreamViewsTableSeeder::class);
        //$this->call(PhotoVerificationsTableSeeder::class);
        //$this->call(ProfilesBackgroundsTableSeeder::class);
        //$this->call(MeetingsTableSeeder::class);
        //$this->call(MeetingReviewsTableSeeder::class);
        //$this->call(CharitiesTableSeeder::class);
        //$this->call(PresentCategoriesTableSeeder::class);
        //$this->call(PresentsTableSeeder::class);
        $this->call(AdminPermissionsTableSeeder::class);
        $this->call(PaymentPercentsTableSeeder::class);
        $this->call(ServicePagesTableSeeder::class);
        $this->call(NotificationsSettingsUsersTableSeeder::class);
        $this->call(SlugsUsersTableSeeder::class);

//        factory(App\Models\User::class, 1000)->create()->each(function ($user) {
//            $user->profile()->save(factory(App\Models\Profile::class)->make());
//        });
//        factory(App\Models\User::class, 100)->create()->each(function ($user) {
//            $user->profile()->save(factory(App\Models\Profile::class)->make());
//            $charityOrganization = $user->charity_organization()->save(factory(App\Models\CharityOrganization::class)->make());
//            $user->auctions()->save(factory(App\Models\Auction::class)->make(['charity_organization_id'=>$charityOrganization->id]));
//        });
    }

}
