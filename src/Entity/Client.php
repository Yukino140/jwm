<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column]
    private ?int $phone_number = null;

    #[ORM\Column]
    private ?int $cin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birth_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $transaction_limit = null;

    #[ORM\Column(length: 255)]
    private ?string $account_type = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Account::class)]
    private Collection $list_account;

    public function __construct()
    {
        $this->list_account = new ArrayCollection();
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(int $phone_number): static
    {
        $this->phone_number = $phone_number;

        return $this;
    }

    public function getCin(): ?int
    {
        return $this->cin;
    }

    public function setCin(int $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birth_date;
    }

    public function setBirthDate(\DateTimeInterface $birth_date): static
    {
        $this->birth_date = $birth_date;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getTransactionLimit(): ?int
    {
        return $this->transaction_limit;
    }

    public function setTransactionLimit(int $transaction_limit): static
    {
        $this->transaction_limit = $transaction_limit;

        return $this;
    }

    public function getAccountType(): ?string
    {
        return $this->account_type;
    }

    public function setAccountType(string $account_type): static
    {
        $this->account_type = $account_type;

        return $this;
    }

    /**
     * @return Collection<int, Account>
     */
    public function getListAccount(): Collection
    {
        return $this->list_account;
    }

    public function addListAccount(Account $listAccount): static
    {
        if (!$this->list_account->contains($listAccount)) {
            $this->list_account->add($listAccount);
            $listAccount->setOwner($this);
        }

        return $this;
    }

    public function removeListAccount(Account $listAccount): static
    {
        if ($this->list_account->removeElement($listAccount)) {
            // set the owning side to null (unless already changed)
            if ($listAccount->getOwner() === $this) {
                $listAccount->setOwner(null);
            }
        }

        return $this;
    }

    public function addAccount(Account $account): self
    {
        if (!$this->list_account->contains($account)) {
            $this->list_account[] = $account;
            $account->setOwner($this);
        }

        return $this;
    }

}
