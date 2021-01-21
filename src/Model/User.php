<?php declare(strict_types = 1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Get the links for User
     */
    public function links()
    {
        return $this->hasMany(Link::class);
    }
}