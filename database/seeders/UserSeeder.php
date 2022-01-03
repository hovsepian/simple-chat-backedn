<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;
use Str;
use Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = ['Davit', 'Garik', 'Aram', 'Gurgen', 'Ani', 'Lia', 'Anahit', 'Srbuhi', 'Karen', 'Artak', 'Anna', 'Tatevik', 'Khachik', 'Luiza', 'Yura', 'Vahe', 'Qristina', 'Tigran', 'Mekhak', 'Narek'];
        $surnames = [
            "Abrahamyan","Adamyan","Aleksanyan","Arakelyan","Arshakyan","Arzumanyan","Aslanyan","Avagyan","Avetisyan","Babayan","Badalyan","Baghdasaryan","Badassyan","Barseghyan","Danielyan","Darbinyan","Davtyan","DemirdjianGabrielyan","Galstyan","Gasparyan","Gevorgyan","Gharibyan","Ghazaryan","Ghukasyan","Grigoryan","Hakobyan","Hambardzumyan","Harutyunyan","Hayrapetyan","Hovhannisyan","Hovsepyan","Karapetyan","Khachatryan","Kirakosyan","Kocharyan","Manukyan","Margaryan","Martirosyan","Melkonyan","Mikayelyan","Minasyan","Mirzoyan","Mkhitaryan","Mkrtchyan","Mnatsakanyan","Muradyan","Nazaryan","Nersisyan","Ohanyan","Petrosyan","Poghosyan","Safaryan","Sahakyan","Sargsyan","SetrakianShahinyan","Simonyan","Soghomonyan","Stepanyan","Timuryan","Tonoyan","Torosyan","Tovmasyan","Vardanyan","Voskanyan","Yeghiazaryan","Yesayan","Zakaryan"
        ];

        DB::table('users')->insert([
            'name' => $names[rand(0, count($names) - 1)] . ' ' . $surnames[rand(0, count($surnames) - 1)],
            'username' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            'password' => Hash::make('password'),
        ]);

    }
}
