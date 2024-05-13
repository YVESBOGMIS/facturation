<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;


#[Route('/auth')]
class AuthController extends AbstractController
{
    #[Route('/register', name: 'auth.register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer): Response
    {
        $body = json_decode($request->getContent(), true);  // Récupération des données


        // Générer un code à 6 caractères
        $code = substr(Uuid::v4(), 0, 6);

        // Création de l'entité
        $user = new User();
        $user->setCodeverification($code);
        $user->setEmail($body['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $body['password']));

        $entityManager->persist($user);  // Générer un ID et préparer la requête
        $entityManager->flush();  // Exécuter la requête SQL

        $email = new Email();
        $email->from('DHI Facturation <no-reply.training@dhi-cm.com>');
        $email->to($body['email']);
        $email->subject('Création de votre compte DHI Facturation');
        $email->html("Votre code de vérification DHI Facturation est <strong>$code</strong> ");
        $mailer->send($email);

        return $this->json($user);
    }


    #[Route('/register/confirm', name: 'auth.register.confirm', methods: ['POST'])]
    public function registerConfirm(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $body = json_decode($request->getContent(), true);  // Récupération des données

        $user = $entityManager->getRepository(User::class)->findOneBy([// vérification du eamail et du codeverifiaction dans bd
            'email' => $body['email'],
            'codeverification' => $body['codeverification']
        ]);

        // Vérifie si l'utilisateur existe
        if ($user != null) {
            // Vérification du mail existant
            $user->setIsVerified(true);
            $entityManager->flush();
            return $this->json($user);
        }

        return $this->json(['message' => 'Code de vérification incorrect'], 400);
    }


    #[Route('/login', name: 'auth.login', methods: ['POST'])]
    public function login(Request                     $request,
                          EntityManagerInterface      $entityManager,
                          UserPasswordHasherInterface $passwordHasher,
                          JWTTokenManagerInterface    $jwt): Response
    {
        $body = json_decode($request->getContent(), true);  // Récupération des données

        // Recherche l'utilisateur
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $body['email']]);

        // Vérifie si l'utilisateur existe
        if ($user != null) {
            // Vérifie si le mot de passe est correct
            if ($passwordHasher->isPasswordValid($user, $body['password'])) {
                // Vérifie si l'utilisateur est vérifié
                if ($user->isVerified()) {
                    return $this->json([
                        'user' => $user,
                        'token' => $jwt->create($user),
                    ]);
                } else {
                    return $this->json(['message' => 'Vous devez vérifier votre compte'], 403);// interdit
                }
            }
        }

        return $this->json(['message' => 'Identifiants incorrects'], 401);
    }


    #[Route('/forgotpassword', name: 'auth.forgotpassword.z', methods: ['POST'])]
    public function format(Request $request, MailerInterface $mailer,
                           EntityManagerInterface $entityManager): JsonResponse
    {
        $body = json_decode($request->getContent(), true);// Récupération des données

        // Vérifier si le tableau $body est null
        if ($body === null) {
            return $this->json(['message' => 'Le corps de
             la requête est vide ou n\'est pas au format JSON.'], 400);
        }
        // Recherche l'utilisateur
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $body['email']]);

        // Générer un code à 6 caractères
        $code = substr(Uuid::v4(), 0, 6);

        $user->setCoderesetpass($code);// met à jour le nouveau code
        $entityManager->persist($user);  // Générer un ID et préparer la requête
        $entityManager->flush();  // Exécuter la requête SQL


// si l'utilisateur est vérifié envoi du mail
        if ($user != null) {
//envoyé un mail de réinitialisation du mot de passe
            $email = new Email();
            $email->from('DHI Facturation <no-reply.training@dhi-cm.com>');
            $email->to($body['email']);
            $email->subject('réinitialisation du mot de passe de votre compte DHI Facturation');
            $email->html("Votre code de réinitialisation de mot de passe DHI Facturation est <strong>$code</strong> ");
            $mailer->send($email);

            return $this->json(['message' => 'Un e-mail de réinitialisation a été envoyé.'], Response::HTTP_OK);
        } else  // si l'utilisateur est vérifié envoie du nouveau code
        {
            return $this->json(['message' => 'Aucun utilisateur trouvé avec cet e-mail.'], 400);
        }

    }


    #[Route('/resetpassword', name: 'auth.register.resetpassword', methods: ['POST'])]
    public function reset(Request                     $request, EntityManagerInterface $entityManager,
                          UserPasswordHasherInterface $passwordHasher): Response
    {
        $body = json_decode($request->getContent(), true);  // Récupération des données
        //Vérifie si l'utilisateur et le code de réinitialisaon existe

        if (!isset($body['coderesetpass']))
        {
            return $this->json(['message' => 'entité incorrect'],400);
        }

        $user = $entityManager->getRepository(User::class)->findOneBy([
            'email' => $body['email'],
            'coderesetpass' => $body['coderesetpass']
        ]);



        if ($user != null) {// si mail et nouveau code existe
            // mettre à jour le nouveau mot de passe
            $user->setPassword($passwordHasher->hashPassword($user, $body['password']));
            $entityManager->flush();// ajouter dans la base de donnée
            return $this->json($user);
        }

        return $this->json(['message' => 'Code de vérification incorrect'], 400);
    }

}