<?php

use Illuminate\Database\Seeder;
use App\Helpers\MediaHelper;
use App\Exceptions\GraphQLSaveDataException;

class PresentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $categories = \App\Models\PresentCategory::all();

        $data = [
            'penguin' => [
                'penguin_cute.svg',
                'penguin_flower.svg',
                'penguin_happy_birthday.svg',
                'penguin_hello.svg',
                'penguin_in_love.svg',
                'penguin_please.svg'
            ],
            'avocado' => [
                'cool_avocado.svg',
                'couple_avocado.svg',
                'cute_avocado.svg',
                'in_love_avocado.svg',
                'kiss_avocado.svg',
                'surprised_avocado.svg',
                'upset_avocado.svg'
            ]
        ];

        $this->storeWithUpload($categories);
//        foreach ($categories as $category) {
//            $stickers = \Illuminate\Support\Arr::get($data, $category->name);
//
//            if(!empty($stickers)) {
//                foreach ($stickers as $sticker) {
//
//                    \App\Models\Present::create([
//                        'category_id' => $category->id,
//                        'image' => $sticker,
//                        'mimetype' => 'image/svg',
//                        'size' => rand(140, 250),
//                        'price' => rand(5, 100),
//                    ]);
//                }
//            }
//        }
    }


    protected function storeWithUpload($categories)
    {
        $path = '/home/lirik0910/projects/buydating/storage/app/public/stickers';

        foreach ($categories as $category) {
            $stor_path = storage_path('app/public/stickers' . '/' . $category->name);
            $file_names = array_diff(scandir($stor_path), array('..', '.'));
            $s3path = MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_IMAGE) . '/' . $category->id;

            foreach ($file_names as $file_name) {
                $file_path = $stor_path . '/' . $file_name;

                MediaHelper::putObject($s3path, pathinfo($file_path)['basename'], $file_path);

//                if (!MediaHelper::checkExists($s3path . '/' . pathinfo($file_path)['basename'])) {
//                    throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
//                }

                \App\Models\Present::create([
                    'category_id' => $category->id,
                    'image' => pathinfo($file_path)['basename'],
                    'mimetype' => 'image/svg',
                    'size' => rand(140, 250),
                    'price' => rand(5, 100),
                ]);
            }
        }
    }


}
