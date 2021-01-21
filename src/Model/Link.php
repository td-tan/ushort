<?php declare(strict_types = 1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    public function user()
    {
        return $this->belongsTo(Link::class);
    }
}