<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route(path="/login", name="login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authUtils)
    {
        return $this->render('security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'last_username' => $authUtils->getLastUsername(),
        ]);
    }

    /**
     * @Route(path="/register", name="register", methods={"GET", "POST"})
     */
    public function register(ManagerRegistry $managerRegistry, UserPasswordEncoderInterface $passwordEncoder, Request $request)
    {
        if ($request->isMethod(Request::METHOD_POST)) {
            try {
                $user = new User($request->request->get('displayname'), $request->request->get('username'));
            } catch (InvalidArgumentException $invalidArgument) {
                return $this->render(
                    'security/register.html.twig', [
                        'error' => [
                            'messageKey' => 'Provided email is not a valid email address.',
                            'messageData' => [],
                        ]
                    ]
                );
            }

            $encodedPassword = $passwordEncoder->encodePassword($user, $request->request->get('password'));
            $user->updatePassword($encodedPassword);

            $entityManager = $managerRegistry->getManagerForClass(User::class);
            $entityManager->persist($user);
            $entityManager->flush();
            $entityManager->clear();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/register.html.twig', [
            'error' => null,
        ]);
    }

    /**
     * @Route(path="/logout", name="logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \RuntimeException('This route will be handled by Symfony\'s security system.');
    }
}
