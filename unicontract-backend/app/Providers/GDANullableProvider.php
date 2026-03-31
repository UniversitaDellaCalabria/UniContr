<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Query\Builder;

class GDANullableProvider extends ServiceProvider
{
    /**
     * Registra la macro globale all'avvio dell'applicazione.
     */
    public function boot()
    {
        Builder::macro('cleanGda', function () {
            $dirty = ['#NULL#', '-999999999', -999999999];
            
            // Esegue la query e pulisce i risultati
            $results = $this->get();

            return $results->map(function ($row) use ($dirty) {
                foreach ($row as $key => $value) {
                    if (in_array($value, $dirty, true)) {
                        if (is_object($row)) {
                            $row->{$key} = null;
                        } else {
                            $row[$key] = null;
                        }
                    }
                }
                return $row;
            });
        });
    }

    public function register()
    {
        //
    }
}
