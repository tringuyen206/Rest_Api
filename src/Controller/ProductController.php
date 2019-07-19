<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Product;
use App\Form\ProductType;

class ProductController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/api/products",name="_test_list_product")
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Fundraiser types",
     * )
     */
    public function getProductAction(Request $request)
    {
        $products = $this->get('doctrine.orm.entity_manager')
            ->getRepository('App:Product')
            ->findAll();

        return $products;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/api/products/{id}",name="_test_product")
     * @SWG\Tag(name="Product")
     * @IsGranted("ROLE_ADMIN")
     * @SWG\Response(
     *     response=200,
     *     description="Returns an array of Fundraiser types",
     * )
     */
    public function getProductsAction(string $id)
    {
        $products = $this->get('doctrine.orm.entity_manager')
     * @IsGranted("ROLE_ADMIN")
            ->getRepository('App:Product')
            ->find($id);

        if (empty($products)) {
            return new JsonResponse(['message' => 'Products not found'], Response::HTTP_NOT_FOUND);
        }

        return $products;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/api/products",name="_test_p_product")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Post(
     *     tags ={"Product"},
     *     @SWG\Parameter(
     *          name="Post Product",
     * 			in="body",
     *          @Model(type=ProductType::class)
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
    public function postProductsAction(Request $request)
    {
        $products = new Product();
        $form = $this->createForm(ProductType::class, $products);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($products);
            $em->flush();
            return $products;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Delete("/api/products/{id}",name="_test_d_product")
     * @SWG\Tag(name="Product")
     * @IsGranted("ROLE_SUPER_ADMIN")
     * @SWG\Response(
     *     response=200,
     *     description="Delete successful!!!",
     * )
     */
    public function removeProductAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $products = $em->getRepository('App:Product')
            ->find($request->get('id'));
        /* @var $user User */

        if ($user) {
            $em->remove($products);
            $em->flush();
        }
    }
}