<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\PayExpenseReport;
use App\Controller\RefuseExpenseReport;
use App\Groups\ExpenseReportGroups;
use App\Repository\ExpenseReportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ExpenseReportRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: [
                'groups' => [ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM],
            ]
        ),
        new GetCollection(
            normalizationContext: [
                'groups' => [ExpenseReportGroups::EXPENSE_REPORT_READ],
            ]
        ),
        new Post(),
        new Patch(),
        new Delete(),
        new Get(
            uriTemplate: '/expense_reports/{id}/pay',
            controller: PayExpenseReport::class,
            openapiContext: [
                'summary' => 'Pay an expense report',
            ],
            normalizationContext: [
                'groups' => [ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM],
            ]
        ),
        new Get(
            uriTemplate: '/expense_reports/{id}/refuse',
            controller: RefuseExpenseReport::class,
            openapiContext: [
                'summary' => 'Refuse an expense report',
            ],
            normalizationContext: [
                'groups' => [ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM],
            ]
        ),
    ]
)]
#[ApiFilter(
    SearchFilter::class, properties: [
        'owner.role' => 'exact',
        'owner.isDeleted' => 'exact',
    ]
)]
class ExpenseReport
{
    const STATUS_EN_COURS = 'en cours';
    const STATUT_PAYE = 'payée';
    const STATUT_REFUSEE = 'refusée';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([ExpenseReportGroups::EXPENSE_REPORT_READ, ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([ExpenseReportGroups::EXPENSE_REPORT_READ, ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM])]
    private ?string $reason = null;

    #[ORM\Column(length: 30, options: ['default' => self::STATUS_EN_COURS])]
    #[Groups([ExpenseReportGroups::EXPENSE_REPORT_READ, ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM])]
    private ?string $status;

    #[ORM\Column]
    #[Groups([ExpenseReportGroups::EXPENSE_REPORT_READ, ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM])]
    private ?float $cost = null;

    #[ORM\ManyToOne(inversedBy: 'expenseReports')]
    #[Groups([ExpenseReportGroups::EXPENSE_REPORT_READ, ExpenseReportGroups::EXPENSE_REPORT_READ_ITEM])]
    private ?User $owner = null;

    public function __construct()
    {
        $this->status = self::STATUS_EN_COURS;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }

    public function setCost(float $cost): static
    {
        $this->cost = $cost;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }
}
