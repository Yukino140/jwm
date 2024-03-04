<?php

namespace App\Entity;

use App\Repository\ResponsibleLoanRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsibleLoanRepository::class)]
class ResponsibleLoan extends Agent
{

}
