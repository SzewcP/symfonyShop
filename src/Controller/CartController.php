<?php
namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends Controller
{
    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @param Session $session
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, Session $session)
    {
        if ($request->get('id')) {
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository(Product::class)->find($request->get('id'));
            if (!empty($session->get('cart'))) {
                $itemCart = $session->get('cart');
                $itemCart[] = $product;
                $session->set('cart', $itemCart);
            } else {
                $itemCart = [];
                $itemCart[] = $product;
                $session->set('cart', $itemCart);
            }
            if (in_array($product, $itemCart)) {
                $this->addFlash('success', sprintf("Success check <a href='/cart'>Shopping cart</a>"));
            } else {
                $this->addFlash('error', 'error');
            }

            return $this->redirectToRoute('product_show', ['id' => $id = $request->get('id')]);
        }
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", methods = "GET|POST")
     */
    public function delete(Request $request, Session $session)
    {

        $id = $request->get('id');
        $itemCart = $session->get('cart');
        foreach ($itemCart as $key => $value) {
            if ($key == $id) {
                unset($itemCart[$id]);
                $itemCart = $session->set('cart', $itemCart);
            }
        }
        return $this->redirectToRoute('cart');
    }
}

