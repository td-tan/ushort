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

    /**
     * Get the token for User
     */
    public function token()
    {
        return $this->hasOne(Token::class);
    }

    /**
     * Compare User
     */
    public function isSame(User $user)
    {
        return $this->id == $user->id;
    }
}