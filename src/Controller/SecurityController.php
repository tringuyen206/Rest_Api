<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use App\Form\UserType;

class SecurityController
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/login_check",name="_test_login")
     * @SWG\Post(
     *     tags ={"Authorization"},
     *     @SWG\Parameter(
     *          name="Get Access Token",
     * 			in="body",
     * 			required=true,
     *          default="Bearer TOKEN",
     *          description="Bearer token",
     *          @Model(type=UserType::class)
     *     ),
     *      @SWG\Response(
     * 			response=200,
     * 			description="success",
     * 		),
     * 		@SWG\Response(
     * 			response="default",
     * 			description="error",
     * 		),
     * )
     */
    public function getUser()
    {
        $user = null;
        $token = $this->tokenStorage->getToken();

        if ($token !== null) {
            $user = $token->getUser();
        }

        return $user;
    }
}