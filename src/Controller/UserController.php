<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'now' => new \DateTime(),
        ]);
    }

    #[IsGranted('SUPER_ADMIN')]
    #[Route('/{id}/to_admin', name: 'app_user_to_admin')]
    public function changeToRoleA(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
        $entityManager->flush();

        $this->addFlash($translator->trans('Succès'), $translator->trans('Rôle admin attribué à l\'utilisateur'));

        return $this->redirectToRoute("app_user");
    }
    #[IsGranted('SUPER_ADMIN')]
    #[Route('/{id}/to_super_admin', name: 'app_admin_to_super_admin')]
    public function changeToRoleSA(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user = $userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $user->setRoles(["ROLE_SUPER_ADMIN", "ROLE_ADMIN"]);
        $entityManager->flush();

        $this->addFlash($translator->trans('Succès'), $translator->trans('Rôle super admin attribué à l\'utilisateur'));

        return $this->redirectToRoute("app_user");
    }

    #[IsGranted('SUPER_ADMIN')]
    #[Route('/{id}/remove/admin_role', name: 'app_user_remove_admin_role')]
    public function removeToRoleA(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user->setRoles([]);
        $entityManager->flush();

        $this->addFlash($translator->trans('Succès'), $translator->trans('Rôle super admin retiré à l\'utilisateur'));

        return $this->redirectToRoute("app_user");
    }
    
    #[IsGranted('SUPER_ADMIN')]
    #[Route('/{id}/remove/super_admin_role', name: 'app_user_remove_super_admin_role')]
    public function removeToRoleSA(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $user->setRoles([]);
        $entityManager->flush();

        $this->addFlash($translator->trans('Erreur'), $translator->trans('Rôle super admin retiré à l\'utilisateur'));

        return $this->redirectToRoute("app_user");
    }

    #[IsGranted('SUPER_ADMIN')]
    #[Route('/{id}/remove/', name: 'app_user_remove_super_admin_role')]
    public function removeToRole(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository, TranslatorInterface $translator): Response
    {
        $userfind->$userRepository->find($id);
        $entityManager->remove($userfind);
        $entityManager->flush();

        $this->addFlash($translator->trans('Erreur'), $translator->trans('Utilisateur supprimé'));

        return $this->redirectToRoute("app_user");
    }
}
