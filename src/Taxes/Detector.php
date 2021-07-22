<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Detector
{

    protected $logger;
    protected $seuil;

    public function __construct(LoggerInterface $logger, float $seuil)
    {
        $this->logger = $logger;
        $this->seuil = $seuil;
    }

    public function detect(float $prix): bool
    {
        $this->logger->info("Une detection a lieu : $prix");
        return $prix > $this->seuil;
    }
}