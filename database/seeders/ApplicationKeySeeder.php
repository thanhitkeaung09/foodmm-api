<?php

namespace Database\Seeders;

use App\Models\ApplicationKey;
use App\Models\AppVersion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ApplicationKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ApplicationKey::query()->create([
            'name' => 'android-v1.0.0',
            'app_id' => generateAppId(),
            'app_secrete' => generateAppSecrete(),
            'obsoleted' => false,
        ]);

        ApplicationKey::query()->create([
            'name' => 'ios-v1.0.0',
            'app_id' => generateAppId(),
            'app_secrete' => generateAppSecrete(),
            'obsoleted' => false,
        ]);

        AppVersion::query()->create([
            'version' => 'v-1.0.0',
            'build_no' => '2039203902932032',
            'is_forced_updated' => false,
            'ios_link' => 'https://foodmm.org/',
            'android_link' => 'https://foodmm.org/',
        ]);
    }
}
