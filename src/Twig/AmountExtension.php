<?php 

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Ajout de filtre Twig
 */
class AmountExtension extends AbstractExtension{

    public function getFilters(){
        return [
            new TwigFilter('amount', [$this, 'amount'])
        ];
    }


    public function amount($value, string $symbol="€", string $decSep=",", string $thousandSep=" "){
        $finalValue = number_format($value, 2, $decSep, $thousandSep)." $symbol";

        return $finalValue;
    }
}