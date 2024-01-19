<?php

namespace App\Controller;

use App\Entity\ExpenseReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class DeleteExpenseReport extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(ExpenseReport $expenseReport): void
    {
        if (!$expenseReport->canBeDeleted()) {
            return;
        }

        $this->entityManager->remove($expenseReport);
        $this->entityManager->flush();
    }
}