<?php

namespace App\Entity;

use App\Entity\enum\AccountType;
use App\Entity\enum\Currency;
use App\Repository\AccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $account_number = null;

    #[ORM\Column(length: 255)]
    private ?AccountType $account_type = null;

    #[ORM\Column(length: 10)]
    private ?Currency $currency = null;

    #[ORM\Column]
    private ?bool $validate = null;

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'list_account')]
    private ?Client $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountNumber(): ?string
    {
        return $this->account_number;
    }

    public function setAccountNumber(string $account_number): static
    {
        $this->account_number = $account_number;

        return $this;
    }

    public function getAccountType(): ?AccountType
    {
        return $this->account_type;
    }

    public function setAccountType(?AccountType $account_type): self
    {
        $this->account_type = $account_type;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function isValidate(): ?bool
    {
        return $this->validate;
    }

    public function setValidate(bool $validate): static
    {
        $this->validate = $validate;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getOwner(): ?Client
    {
        return $this->owner;
    }

    public function setOwner(?Client $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
