<?php
namespace App\Controller\Api;

use App\Entity\Product;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends FOSRestController
{

    /**
     * @FOSRest\Get("/products")
     */
    public function getProducts()
    {

        $repository = $this->getDoctrine()->getRepository(Product::class);

        $products = $repository->findAll();
        $arr = array();
        foreach ($products as $product) {
            $name = $product->getName();
            $id = $product->getId();
            $price = $product->getPrice();
            $arr[] = array('id' => $id, 'name' => $name, 'price' => $price);
        }
        if (empty($arr)) {
            throw new HttpException(404, 'Products not Found');
        }
        $view = View::create($arr, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @FOSRest\Get("/product/{id}")
     * @param $id
     * @return Response
     */
    public function getProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if (!$product) {
            throw new HttpException(404, 'Product not Found');
        }
        $view = $this->view($product, Response::HTTP_OK);
        return $this->handleView($view);
    }

    /**
     * @FOSRest\Post("/new/product")
     * @param Request $request
     * @return Response
     */
    public function postProduct(Request $request)
    {
        $product = new Product();
        $product->setName($request->get('name'));
        $product->setDescription($request->get('description'));
        $product->setPrice($request->get('price'));
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();
        if ($product->getId() == null) {
            throw new HttpException(400, 'Bad request');
        }
        $view = $this->view($product, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @FOSRest\Put("/edit/product/{id}")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function editProduct($id, Request $request)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($request->get('name')) {
            $product->setName($request->get('name'));
        }
        if ($request->get('description')) {
            $product->setDescription($request->get('description'));
        }
        if ($request->get('price')) {
            $product->setPrice($request->get('price'));
        }
        $this->getDoctrine()->getManager()->flush();
        $view = $this->view($product, Response::HTTP_OK);

        return $this->handleView($view);
    }

    /**
     * @FOSRest\Delete("/delete/product/{id}")
     * @param $id
     * @return View
     */
    public function deleteProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            if ($product->getId()) {
                throw new HttpException(400, 'Bad request');
            }

            return View::create('Product Deleted', Response::HTTP_OK);
        }
    }

    /**
     * Returns a 404 Not Found
     * @param string $message
     */


}
