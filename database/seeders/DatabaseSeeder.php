<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Saving;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $member = Member::create([
            'member_no'=>'AGT-001',
            'name'=>'Anggota Contoh',
            'phone'=>'081234567890',
            'email'=>'anggota@example.com',
            'address'=>'Alamat contoh',
            'join_date'=>now()->toDateString(),
            'status'=>'active'
        ]);

        Saving::create([
            'member_id'=>$member->id,
            'type'=>'pokok',
            'amount'=>100000,
            'transaction_date'=>now()->toDateString(),
            'description'=>'Data contoh'
        ]);
    }
}
