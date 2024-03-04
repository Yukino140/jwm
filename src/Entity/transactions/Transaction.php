<?php

namespace App\Entity\transactions;

use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $account_number = null;

    #[ORM\Column(length: 255)]
    private ?string $transaction_type = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $fee = null;

    #[ORM\Column]
    private ?float $authenticator_code = null;

    #[ORM\Column(length: 255)]
    private ?string $receiver_account_number = null;

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

    public function getTransactionType(): ?string
    {
        return $this->transaction_type;
    }

    public function setTransactionType(string $transaction_type): static
    {
        $this->transaction_type = $transaction_type;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getFee(): ?float
    {
        return $this->fee;
    }

    public function setFee(float $fee): static
    {
        $this->fee = $fee;

        return $this;
    }

    public function getAuthenticatorCode(): ?float
    {
        return $this->authenticator_code;
    }

    public function setAuthenticatorCode(float $authenticator_code): static
    {
        $this->authenticator_code = $authenticator_code;

        return $this;
    }

    public function getReceiverAccountNumber(): ?string
    {
        return $this->receiver_account_number;
    }

    public function setReceiverAccountNumber(string $receiver_account_number): static
    {
        $this->receiver_account_number = $receiver_account_number;

        return $this;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
