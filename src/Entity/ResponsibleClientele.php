<?php

namespace App\Entity;

use App\Repository\ResponsibleClienteleRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsibleClienteleRepository::class)]
class ResponsibleClientele extends Agent
{

}
