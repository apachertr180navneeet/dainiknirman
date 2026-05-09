<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settingsData = [
            [
                'title' => 'Site Title',
                'slug' => 'site-title',
                'value' => 'JaiHarsh',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Site Logo',
                'slug' => 'site-logo',
                'value' => 'verified_image.jpg',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Logo Title',
                'slug' => 'logo-title',
                'value' => 'JaiHarsh',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Reserved Right',
                'slug' => 'reserved-right',
                'value' => '&copy; JaiHarsh',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'SMTP',
                'slug' => 'smtp',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'SMTP Host',
                'slug' => 'smtp-host',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'SMTP Port',
                'slug' => 'smtp-port',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'SMTP Username',
                'slug' => 'smtp-username',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'SMTP Password',
                'slug' => 'smtp-password',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'From Email',
                'slug' => 'from-email',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'From Name',
                'slug' => 'from-name',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Show Items Per Page',
                'slug' => 'show-items-per-page',
                'value' => '10',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'App Version',
                'slug' => 'app-version',
                'value' => '1.0',
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Android Legacy Key',
                'slug' => 'android-legacy-key',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'QR Code Image',
                'slug' => 'qr-code-image',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Banner Image',
                'slug' => 'banner-image',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'UPI ID',
                'slug' => 'upi-id',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
            [
                'title' => 'Service Plan',
                'slug' => 'service-plan',
                'value' => null,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ],
        ];

        foreach($settingsData as $key => $value)
        {
            $isSettingExists = Setting::where('title', $value['title'])->exists();

            if(!$isSettingExists)
            {
                Setting::create($value);
            }
        }
    }
}
