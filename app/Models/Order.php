<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

#[Fillable(['customer_id', 'data_evento', 'local_evento', 'tipo_evento', 'convidados', 'status', 'enviado_em', 'observacoes', 'total'])]
class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->token ??= Str::random(40);
        });
    }

    protected function casts(): array
    {
        return [
            'data_evento' => 'date',
            'enviado_em' => 'datetime',
            'convidados' => 'integer',
            'total' => 'decimal:2',
            'status' => OrderStatus::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function numero(): string
    {
        return '#'.str_pad((string) $this->id, 3, '0', STR_PAD_LEFT);
    }

    public function valorPorPessoa(): float
    {
        return (float) $this->items->sum('valor_unitario');
    }

    /**
     * @return Collection<string, Collection<int, OrderItem>>
     */
    public function itensPorCategoria(): Collection
    {
        return $this->items
            ->sortBy(fn (OrderItem $item) => sprintf('%05d-%s', $item->product->category->ordem, $item->product->name))
            ->groupBy(fn (OrderItem $item) => $item->product->category->name);
    }

    public function recalcularTotal(): void
    {
        $this->load('items');

        $this->update(['total' => $this->valorPorPessoa() * $this->convidados]);
    }
}
