<?php

namespace Database\Seeders;

use App\Enums\AssetEnum;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Database\Seeder;

class TempDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userAhmad = User::create([
            'name' => 'ahmad',
            'email' => 'ahmad@test.com',
            'password' => 'password',
        ]);
        WalletService::getWallet(AssetEnum::RIAL , $userAhmad)->addTransaction(800000000 , 'موجودی اولیه');
        WalletService::getWallet(AssetEnum::GOLD , $userAhmad)->addTransaction(7,'موجودی اولیه');

        $userReza = User::create([
            'name' => 'reza',
            'email' => 'reza@test.com',
            'password' => 'password',
        ]);
        WalletService::getWallet(AssetEnum::RIAL , $userReza)->addTransaction(740000000,'موجودی اولیه');
        WalletService::getWallet(AssetEnum::GOLD , $userReza)->addTransaction(18,'موجودی اولیه');

        $userAkbar = User::create([
            'name' => 'akbar',
            'email' => 'akbar@test.com',
            'password' => 'password',
        ]);
        WalletService::getWallet(AssetEnum::RIAL , $userAkbar)->addTransaction(330000000,'موجودی اولیه');
        WalletService::getWallet(AssetEnum::GOLD , $userAkbar)->addTransaction(50,'موجودی اولیه');

    }
}
