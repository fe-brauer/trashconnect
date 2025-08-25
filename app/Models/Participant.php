<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    protected $fillable = [
        'season_id','candidate_id','role','placement','prize_won',
    ];

    public function season() {
        return $this->belongsTo(Season::class);
    }

    public function candidate() {
        return $this->belongsTo(Candidate::class);
    }
}
