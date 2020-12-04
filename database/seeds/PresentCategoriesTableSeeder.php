<?php

use App\Exceptions\GraphQLSaveDataException;
use App\Helpers\MediaHelper;
use Illuminate\Database\Seeder;
use App\Models\PresentCategory;

class PresentCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     * @throws GraphQLSaveDataException
     */
    public function run()
    {
        $data = [
            [
                'name' => 'avocado',
                'image' => 'avocado.svg'
            ],
            [
                'name' => 'raccoon',
                'image' => 'raccoon.svg'
            ],
        ];

        $this->upload();
        $this->storeCategories($data);

    }

    protected function upload()
    {
        $path = '/home/lirik0910/projects/buydating/storage/app/public/categories';
        $stor_path = storage_path('app/public/categories');
        $file_names = array_diff(scandir($stor_path), array('..', '.'));

        $i = 0;

        foreach ($file_names as $file_name) {
            $file_path = $stor_path . '/' . $file_name;
            $file = file_get_contents($file_path);

             $uploaded[] = MediaHelper::putObject(MediaHelper::getS3Path(MediaHelper::FILE_TYPE_PRESENT_CATEGORY_IMAGE), pathinfo($file_path)['basename'], $file_path);
        }

        return $data ?? [];
    }

    /**
     * @param array $data
     * @return array
     * @throws GraphQLSaveDataException
     * @throws \App\Exceptions\GraphQLLogicRestrictException
     */
    protected function storeCategories(array $data)
    {
        $files_types = MediaHelper::FILE_TYPE_PRESENT_CATEGORY_IMAGE;
        $s3path = MediaHelper::getS3Path($files_types);

        foreach ($data as $file_data) {
//            if (!MediaHelper::checkExists($s3path . '/' . $file_data['name'])) {
//                throw new GraphQLSaveDataException(__('media.media_file_not_exists'), __('Error'));
//            }

            $categories[] = [
                'name' => explode('.', $file_data['name'])[0],
                'image' => $file_data['image'],
                'mimetype' => 'image/svg',
                'size' => rand(140, 250),
            ];
        }

        PresentCategory::insert($categories);

        return $categories ?? [];
    }
}
