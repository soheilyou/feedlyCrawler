<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FeedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "feedly_id" => $this->faker->numberBetween(0, 10000000),
            "url" => $this->faker->url,
            "rss_path" => $this->faker->slug,
            "update_period_in_minute" => $this->faker->numberBetween(1, 1000),
            "last_build_date" => $this->faker->dateTimeBetween(
                "-30 days",
                "now"
            ),
            "last_crawled_at" => $this->faker->dateTimeBetween(
                "-30 days",
                "now"
            ),
        ];
    }
}
