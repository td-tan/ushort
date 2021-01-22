<?php declare(strict_types = 1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    /**
     * Get user that created short links
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}