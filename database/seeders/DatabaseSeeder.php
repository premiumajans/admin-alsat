<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\About;
use App\Models\AboutTranslation;
use App\Models\Admin;
use App\Models\Advert;
use App\Models\AdvertDescription;
use App\Models\MetaTag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            LanguageSeeder::class,
            CategorySeeder::class,
            CitySeeder::class,
            ModeSeeder::class,
            EducationSeeder::class,
            ExperienceSeeder::class,
            SalarySeeder::class,
            PermissionsSeeder::class,
            UserSeeder::class,
            AdminSeeder::class,
            PackageSeeder::class,
            TermSeeder::class,
        ]);
        $advert = Advert::create([
            'user_id' => 1,
        ]);
        $description = new AdvertDescription();
        $description->title = 'adas';
        $description->short_description = 'dadsa';
        $description->description = 'adas';
        $description->salary = 3;
        $description->owner = 'sad';
        $description->phone = 444454;
        $advert->description()->save($description);
    }
}
