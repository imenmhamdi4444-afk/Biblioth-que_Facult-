<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BorrowingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class ProfileController extends AbstractController
{
    #[Route('/my-profile', name: 'app_profile')]
    #[IsGranted('ROLE_USER')]
    public function index(BorrowingRepository $borrowingRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $student = $user->getStudent();
        
        $myBorrowings = [];
        if ($student) {
            $myBorrowings = $borrowingRepository->findBy(['student' => $student], ['dateBorrowed' => 'DESC']);
        }

        return $this->render('profile/index.html.twig', [
            'borrowings' => $myBorrowings,
            'student' => $student
        ]);
    }
}
