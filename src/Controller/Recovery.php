<?php

namespace App\Controller;

use App\Entity\User;
use Cavesman\Config;
use Cavesman\Db;
use Cavesman\Http\JsonResponse;
use Cavesman\JWT;
use Cavesman\Mail;
use Cavesman\Request;
use Cavesman\Translate;
use Cavesman\Twig;
use Doctrine\ORM\Exception\ORMException;
use Exception;

final class Recovery
{

    /**
     * Send recovery password email to existing user
     */
    public static function send(): JsonResponse
    {
        try {
            $email = Request::post('email');

            $em = Db::getManager();

            $user = $em->getRepository(User::class)->findOneBy(['email' => $email, 'deletedOn' => null]);

            if (!$user)
                return new JsonResponse(['message' => 'recovery.email.error'], 404);

            $jwt = base64_encode(JWT::encode(['email' => $user->getEmail()]));

            $link = Config::get('api.frontend') . '/recovery/' . $jwt;

            $body = Twig::render('mail/partial/recovery.html.twig', ['message' => Translate::get('mail.recovery.message'), 'server' => $_SERVER, 'link' => ['label' => Translate::get('mail.recovery.link.label'), 'url' => $link]]);
            $text = Twig::renderFromString(Translate::get('mail.recovery.message'), ['message' => Translate::get('mail.recovery.message'), 'server' => $_SERVER]);


            Mail::send(Config::get('mail.test.address'), Translate::get('mail.recovery.subject'), ['html' => $body, 'text' => $text]);

            return new JsonResponse(['message' => 'mail.recovery.response.success']);

        } catch (Exception $e) {
            return new JsonResponse(['message' => 'mail.recovery.response.error', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send recovery password email to existing user
     */
    public static function changePassword(string $token): JsonResponse
    {
        try {

            $password = password_hash(Request::post('password'), PASSWORD_BCRYPT);

            $em = Db::getManager();

            $jwt = JWT::decode(base64_decode($token));

            $user = $em->getRepository(User::class)->findOneBy(['email' => $jwt->email, 'deletedOn' => null]);

            if (!$user)
                return new JsonResponse(['message' => 'recovery.email.error'], 404);

            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            return new JsonResponse(['message' => 'recovery.response.success']);

        } catch (Exception|ORMException $e) {
            return new JsonResponse(['message' => 'recovery.response.error', 'error' => $e->getMessage()], 500);
        }
    }
}
