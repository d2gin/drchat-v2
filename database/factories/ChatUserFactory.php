<?php

namespace Database\Factories;

use App\Api\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatUserFactory extends Factory
{

    protected $model = User::class;

    protected static $usernameInserted = [];

    public function configure()
    {
        return parent::configure();
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $model = $this->newModel();
        while (1) {
            $username = $this->faker->userName();
            if (!in_array($username, self::$usernameInserted, true) && !$model->newQuery()->where('username', $username)->count()) {
                break;
            }
        }
        self::$usernameInserted[] = $username;
        return [
            'username'  => $username,
            'nickname'  => $this->faker->name,
            'password'  => $this->newModel()->passwordHash(111111),
            'avatar'    => 'https://s2.loli.net/2022/09/19/WbC82gHqt3S465w.png',
            'sex'       => rand(0, 1),
            'city'      => $this->faker->city,
            'is_online' => 0,
            'signature' => $this->faker->realText(50),
        ];
    }
}
