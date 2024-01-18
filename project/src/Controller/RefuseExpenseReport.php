<?php

namespace App\Controller;

use App\Entity\ExpenseReport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RefuseExpenseReport extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function __invoke(ExpenseReport $expenseReport)
    {
        $expenseReport->setStatus(ExpenseReport::STATUT_REFUSEE);

        $this->entityManager->flush();

        return $expenseReport;
    }
}