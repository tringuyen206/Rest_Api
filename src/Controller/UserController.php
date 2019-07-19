<?php


namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Entity\User;
use App\Form\UserType;

class UserController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/users",name="_test_list")
     * @SWG\Tag(name="User")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Fundraiser types",
     * )
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->get('doctrine.orm.entity_manager')
            ->getRepository('App:User')
            ->findAll();
        /* @var $users User[] */

        return $users;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/users/{id}",name="_test")
     * @SWG\Tag(name="User")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Fundraiser types",
     * )
     */
    public function getUserAction(string $id)
    {
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('App:User')
            ->find($id);
        /* @var $user User */

        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/users",name="_test_p")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Post(
     *     tags ={"User"},
     *     @SWG\Parameter(
     *          name="user",
     * 			in="body",
     * 			required=true,
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
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/api/users/{id}",name="_test_d")
     * @SWG\Tag(name="User")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Response(
     *     response=200,
     *     description="Delete successful!!!",
     * )
     */
    public function removeUserAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $em->getRepository('App:User')
            ->find($request->get('id'));
        /* @var $user User */

        if ($user) {
            $em->remove($user);
            $em->flush();
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}", name="_test_u")
     * @SWG\Put(
     *     tags ={"User"},
     *
     * )
     */
    public function updateUserAction(Request $request)
    {
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('App:User')
            ->find($request->get('id')); // L'identifiant en tant que paramètre n'est plus nécessaire
        /* @var $user User */

        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->merge($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }
}