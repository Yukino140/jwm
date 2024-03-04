<?php

namespace App\Entity;

use App\Repository\ResponsibleInvestmentRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsibleInvestmentRepository::class)]
class ResponsibleInvestment extends Agent
{

}
