<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GDANullable extends Model
{
    // Valori "sporchi" definiti una volta sola per coerenza
    protected $oracleDirtyValues = ['#NULL#', '-999999999', -999999999];

    /**
     * 1. Gestisce l'accesso puntuale: $model->campo o $model['campo']
     */
    public function getAttributeValue($key)
    {
        $value = parent::getAttributeValue($key);
        return in_array($value, $this->oracleDirtyValues, true) ? null : $value;
    }

    /**
     * 2. Gestisce la conversione massiva: $model->toArray() o json_encode($model)
     * Utile se il tuo QueryBuilder trasforma il risultato in array alla fine.
     */
    public function toArray()
    {
        $attributes = parent::toArray();

        return array_map(function ($value) {
            return in_array($value, $this->oracleDirtyValues, true) ? null : $value;
        }, $attributes);
    }
}
