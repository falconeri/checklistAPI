<?php
/**
 * Created by PhpStorm.
 * Filename: Checklist.php
 * User: falconerialta@gmail.com
 * Date: 2019-02-25
 * Time: 14:04
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Checklist extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'object_domain', 'object_id', 'description', 'is_completed', 'completed_at', 'updated_by', 'due', 'urgency'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];
}