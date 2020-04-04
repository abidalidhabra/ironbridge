<?php

namespace App\Models\v3;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class UserQA extends Eloquent
{
	protected $table = 'user_answers';

    protected $fillable = ['user_id', 'answers'];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'answers'=> [
        	'robo_intro'=> null,
        	'avatar_i_dont_liked'=> null,
        	'avatar_i_cant_afford'=> null,
        	'avatar_i_dont_cares'=> null,
            'avatar_changed_outfit'=> null,
            'robo_final'=> null,
            'robo_random_one'=> null,
        ]
    ];
}
