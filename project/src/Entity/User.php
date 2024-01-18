<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateUser;
use App\Controller\GetUserExpenses;
use App\Groups\ExpenseReportGroups;
use App\Groups\UserGroups;
use App\Repository\UserRepository;
use App\State\Processor\DeleteUserStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => [UserGroups::USER_READ, UserGroups::USER_READ_ITEM],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => [UserGroups::USER_READ],
            ]
        ),
        new Post(
            controller: CreateUser::class,
            normalizationContext: [
                'groups' => [UserGroups::USER_READ_ITEM],
            ]
        ),
        new Patch(),
        new Delete(
            processor: DeleteUserStateProcessor::class,
        ),
        new Get(
            uriTemplate: '/users/{id}/expense_reports',
            controller: GetUserExpenses::class,
            openapiContext: [
                'summary' => 'Get expenses of a user',
            ]
        )
    ]
)]
#[ApiFilter(SearchFilter::class)]
#[ApiFilter(OrderFilter::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([UserGroups::USER_READ, UserGroups::USER_READ_ITEM])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups([UserGroups::USER_READ, UserGroups::USER_READ_ITEM])]
    private ?string $mail = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Groups([UserGroups::USER_READ, UserGroups::USER_READ_ITEM])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        UserGroups::USER_READ,
        UserGroups::USER_READ_ITEM,
        ExpenseReportGroups::EXPENSE_REPORT_READ,
        ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM
    ])]
    private ?string $fullname = null;

    #[ORM\Column(options: ['default' => false])]
    #[Groups([UserGroups::USER_READ, UserGroups::USER_READ_ITEM])]
    private ?bool $isDeleted;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([UserGroups::USER_READ_ITEM])]
    private ?Role $role = null;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: ExpenseReport::class)]
    #[Groups([UserGroups::USER_READ_ITEM])]
    private Collection $expenseReports;

    public function __construct()
    {
        $this->isDeleted = false;
        $this->expenseReports = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): static
    {
        $this->mail = $mail;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): static
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(?Role $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, ExpenseReport>
     */
    public function getExpenseReports(): Collection
    {
        return $this->expenseReports;
    }

    public function addExpenseReport(ExpenseReport $expenseReport): static
    {
        if (!$this->expenseReports->contains($expenseReport)) {
            $this->expenseReports->add($expenseReport);
            $expenseReport->setOwner($this);
        }

        return $this;
    }

    public function removeExpenseReport(ExpenseReport $expenseReport): static
    {
        if ($this->expenseReports->removeElement($expenseReport)) {
            // set the owning side to null (unless already changed)
            if ($expenseReport->getOwner() === $this) {
                $expenseReport->setOwner(null);
            }
        }

        return $this;
    }

    public function canBeDeleted(): bool
    {
        foreach ($this->getExpenseReports() as $expenseReport) {
            if (ExpenseReport::STATUS_EN_COURS === $expenseReport->getStatus()) {
                return false;
            }
        }

        return true;
    }

    public function getRoles(): array
    {
        return [$this->getRole()->getTitle()];
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getMail();
    }
}
