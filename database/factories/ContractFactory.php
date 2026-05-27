<?php

namespace Database\Factories;

use App\Models\Novel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
  public function definition(): array
  {
    return [
      'novel_id' => Novel::factory(),
      'author_id' => User::factory(),
      'editor_id' => User::factory(),
      'contract_type' => $this->faker->randomElement(['exclusive', 'non-exclusive']),
      'revenue_share_author' => 50,
      'revenue_share_platform' => 50,

      // Factory menggunakan forceFill, jadi ini aman meskipun tidak ada di $fillable model
      'status' => 'active',
      'signed_at' => $this->faker->dateTimeBetween('-2 months', 'now'),

      // 🌟 Data KYC (Termasuk Gambar Dummy untuk bypass error 1364)
      'real_name' => $this->faker->name(),
      'id_card_number' => $this->faker->numerify('################'),
      'id_card_image' => 'kyc/dummy-ktp.jpg', // Fix Error
      'selfie_image' => 'kyc/dummy-selfie.jpg', // Fix Error

      // 🌟 Data Bank
      'bank_name' => $this->faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']),
      'bank_account_number' => $this->faker->numerify('##########'),
      'bank_account_name' => $this->faker->name(),

      // 🌟 Data Dokumen Digital
      'signature_image_path' => 'contracts/dummy-signature.png',
      'contract_document_path' => 'contracts/dummy-document.pdf',
    ];
  }
}
