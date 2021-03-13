<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Class Drug
 * @package App\Models
 */
class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    protected $perPage = 5;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function substances()
    {
        return $this->belongsToMany(Substance::class);
    }

    /**
     * Scope a query to only include visible drugs 
     * (containing only visible substances)
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query)
    {
        return $query->whereDoesntHave('substances', function($query) {
            $query->where('visible', false);
        });
    }

    /**
     * Scope a query to only include drugs 
     * without substances outside the given array
     * 
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  Array $substances
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOnly($query, $substances)
    {
        return $query->whereDoesntHave('substances', function($query) use ($substances) {
            $query->whereNotIn('id', $substances);
        });
    }
}
