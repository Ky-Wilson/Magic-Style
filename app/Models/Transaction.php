<?php
namespace App\Models;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'order_id', 'mode', 'status'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}