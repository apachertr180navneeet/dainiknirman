<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cms;

class CmsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cmsData = [
            [
                "title" => "About Us",
                "slug" => "about-us",
                "description" => "About description.",
                "meta_title" => "About Dainik Nirman",
                "meta_keywords" => "About Dainik Nirman",
                "meta_description" => "About Dainik Nirman",
                "created_by" => 1,
                "updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ],
            [
                "title" => "Contact Us",
                "slug" => "contact-us",
                "description" => "Contact page description.",
                "meta_title" => "Contact page Dainik Nirman",
                "meta_keywords" => "Contact page Dainik Nirman",
                "meta_description" => "Contact page Dainik Nirman",
                "created_by" => 1,
                "updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ],
            [
                "title" => "Terms & Conditions",
                "slug" => "terms-conditions",
                "description" => "Terms and conditions page description.",
                "meta_title" => "Terms and conditions page Dainik Nirman",
                "meta_keywords" => "Terms and conditions page Dainik Nirman",
                "meta_description" => "Terms and conditions page Dainik Nirman",
                "created_by" => 1,
                "updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ],
            [
                "title" => "Privacy Policy",
                "slug" => "privacy-policy",
                "description" => "Privacy policy page description.",
                "meta_title" => "Privacy policy page Dainik Nirman",
                "meta_keywords" => "Privacy policy page Dainik Nirman",
                "meta_description" => "Privacy policy page Dainik Nirman",
                "created_by" => 1,
                "updated_by" => 1,
                "created_at" => date("Y-m-d H:i:s"),
                "updated_at" => date("Y-m-d H:i:s")
            ]
        ];

        foreach ($cmsData as $key => $value)
        {
            $isCmsExists = Cms::where('slug', $value['slug'])->first();

            if(!$isCmsExists)
            {
                $cms = Cms::create($value);
            }
        }
    }
}
