<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\DeleteAccountFormType;
use App\Form\ChangePasswordFormType;
use App\Form\ImageFormType;
use App\Form\UserFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    #[Route('/{_locale}/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('dashboard/index.html.twig');
    }

    #[Route('/{_locale}/dashboard/profile', name: 'app_profile')]
    public function profile(Request $request): Response
    {
        $image = new Image();
        $imageForm = $this->createForm(ImageFormType::class, $image);
        $imageForm->handleRequest($request);
        if ($imageForm->isSubmitted() && $imageForm->isValid()) {
            $image = $imageForm->getData();
            $this->addFlash(
                'status-image',
                'image-updated'
            );
            return $this->redirectToRoute('app_profile');
        }

        $user = $this->getUser();
        $userForm = $this->createForm(UserFormType::class, $user);
        $userForm->handleRequest($request);

        if ($userForm->isSubmitted() && $userForm->isValid()) {
            $user = $userForm->getData();
            $this->addFlash(
                'status-profile-information',
                'user-updated'
            );
            return $this->redirectToRoute('app_profile');
        }

        $passwordForm = $this->createForm(ChangePasswordFormType::class, $user);
        $passwordForm->handleRequest($request);

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $user = $passwordForm->getData();
            $this->addFlash(
                'status-password',
                'password-updated'
            );
            return $this->redirectToRoute('app_profile');
        }

        $deleteAccountForm = $this->createForm(DeleteAccountFormType::class, $user);
        $deleteAccountForm->handleRequest($request);
        
        if ($deleteAccountForm->isSubmitted() && $deleteAccountForm->isValid()) {
            $user = $deleteAccountForm->getData();
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('dashboard/edit.html.twig', [
            'imageForm' => $imageForm,
            'userForm' => $userForm,
            'passwordForm' => $passwordForm,
            'deleteAccountForm' => $deleteAccountForm,
        ]);
    }
}
