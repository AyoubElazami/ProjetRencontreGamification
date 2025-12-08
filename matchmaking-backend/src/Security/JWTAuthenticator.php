<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

class JWTAuthenticator extends AbstractAuthenticator
{
    private UserProviderInterface $userProvider;

    public function __construct(UserProviderInterface $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization') 
            && str_starts_with($request->headers->get('Authorization', ''), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('No token provided');
        }

        $tokenString = substr($authHeader, 7); // Enlever "Bearer "

        try {
            // Décoder le payload JWT manuellement
            $parts = explode('.', $tokenString);
            if (count($parts) !== 3) {
                throw new AuthenticationException('Invalid token format');
            }

            // Décoder le payload (base64url)
            $payloadEncoded = $parts[1];
            
            // Base64URL decode avec padding
            $remainder = strlen($payloadEncoded) % 4;
            if ($remainder) {
                $padlen = 4 - $remainder;
                $payloadEncoded .= str_repeat('=', $padlen);
            }
            
            $payloadJson = base64_decode(strtr($payloadEncoded, '-_', '+/'), true);
            
            if ($payloadJson === false) {
                throw new AuthenticationException('Invalid token: could not decode base64');
            }
            
            $payload = json_decode($payloadJson, true);
            
            if (!$payload || !is_array($payload)) {
                throw new AuthenticationException('Invalid token: could not decode JSON payload');
            }

            // LexikJWTBundle stocke getUserIdentifier() dans 'username' du payload
            // getUserIdentifier() retourne l'email dans notre cas
            $identifier = $payload['username'] ?? null;
            
            if (!$identifier) {
                throw new AuthenticationException('Invalid token: no username in payload. Payload keys: ' . implode(', ', array_keys($payload)));
            }

            // Charger l'utilisateur via le UserProvider
            // Le UserProvider cherche par email (configuré dans security.yaml: property: email)
            return new SelfValidatingPassport(
                new UserBadge(
                    $identifier, 
                    function (string $identifier) {
                        try {
                            return $this->userProvider->loadUserByIdentifier($identifier);
                        } catch (UserNotFoundException $e) {
                            throw new AuthenticationException('User not found: ' . $identifier);
                        }
                    }
                )
            );
        } catch (AuthenticationException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new AuthenticationException('Authentication failed: ' . $e->getMessage());
        }
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null; // continuer la requête normalement
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse([
            'code' => 401,
            'message' => $exception->getMessage()
        ], 401);
    }
}
