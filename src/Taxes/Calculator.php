<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Calculator extends AbstractController{

    protected $logger;
    protected $tva;

    public function __construct(LoggerInterface $logger, float $tva){
        $this->logger = $logger;
        $this->tva = $tva;
    }

    public function calcul_tva(float $prix): float{
        $this->logger->info("Un calcul a lieu : $prix");
        return $prix * 0.2;
    }

}