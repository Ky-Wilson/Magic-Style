<?php
// Modèle Order amélioré
namespace App\Models;

use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'subtotal', 'discount', 'tax', 'total',
        'name', 'phone', 'locality', 'address', 'city', 
        'state', 'country', 'landmark', 'zip'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function orderItems(): HasMany {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction(): HasOne {
        return $this->hasOne(Transaction::class);
    }
}
