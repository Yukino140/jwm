<?php

namespace App\Entity;

use App\Repository\ResponsibleAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsibleAccountRepository::class)]
class ResponsibleAccount extends Agent
{

}
