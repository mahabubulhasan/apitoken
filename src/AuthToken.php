<?php
namespace Uzzal\ApiToken;


use Illuminate\Database\Eloquent\Model;

/**
 *
 * @author Mahabubul Hasan <codehasan@gmail.com>
 */
class AuthToken extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'auth_tokens';

    /**
     *
     * @var integer
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','token'];

}