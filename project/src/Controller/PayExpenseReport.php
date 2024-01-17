<?php

namespace App\Controller;

use App\Entity\ExpenseReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayExpenseReport extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(ExpenseReport $expenseReport)
    {
        $expenseReport->setStatus(ExpenseReport::STATUT_PAYE);

        $this->entityManager->flush();
    }
}