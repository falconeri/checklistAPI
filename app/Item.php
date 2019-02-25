<?php
/**
 * Created by PhpStorm.
 * Filename: item.php
 * User: falconerialta@gmail.com
 * Date: 2019-02-25
 * Time: 22:51
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'checklist_id', 'description', 'is_completed', 'completed_at', 'due', 'urgency', 'updated_by'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'checklist_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function checklist() {
        return $this->belongsTo('App\Checklist');
    }
}