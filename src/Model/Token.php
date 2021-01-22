<?php declare(strict_types = 1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    /**
     * Get user that has token
     */
    public function user()
    {
        return $this->belongsTo(Token::class);
    }
}