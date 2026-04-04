<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use SoftDeletes;
    protected $table = 'tr_evaluations';
    protected $fillable = ['internship_id', 'evaluator_id', 'evaluation_date', 'score', 'description', 'type'];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }
}
