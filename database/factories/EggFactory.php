<?php

   namespace Database\Factories;

   use App\Models\Egg;
   use App\Models\User;
   use App\Models\Pen;
   use Illuminate\Database\Eloquent\Factories\Factory;

   class EggFactory extends Factory
   {
       protected $model = Egg::class;

       public function definition()
       {
           $small_eggs = $this->faker->numberBetween(0, 100);
           $medium_eggs = $this->faker->numberBetween(0, 100);
           $large_eggs = $this->faker->numberBetween(0, 100);
           $total_eggs = $small_eggs + $medium_eggs + $large_eggs;
           $crates = round($total_eggs / 30, 2); // Assuming 30 eggs per crate

           return [
               'pen_id' => Pen::factory(),
               'crates' => $crates,
               'small_eggs' => $small_eggs,
               'medium_eggs' => $medium_eggs,
               'large_eggs' => $large_eggs,
               'cracked_eggs' => $this->faker->numberBetween(0, 10),
               'collected_by' => User::factory(),
               'date_laid' => $this->faker->dateTimeBetween('-1 year', 'now'),
               'created_by' => User::factory(),
               'created_at' => now(),
               'updated_at' => now(),
           ];
       }
   }
