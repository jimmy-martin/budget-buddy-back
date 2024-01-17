<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetUserExpenses extends AbstractController
{
    public function __invoke(User $user)
    {
        return $user->getExpenseReports();
    }
}