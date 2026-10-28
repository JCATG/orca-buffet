<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pendente = 'pendente';
    case Enviado = 'enviado';

    public function label(): string
    {
        return match ($this) {
            self::Pendente => 'Pendente',
            self::Enviado => 'Enviado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pendente => 'amber',
            self::Enviado => 'green',
        };
    }
}
