<?php
namespace App\Controller;

use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/", name="product_index", methods="GET")
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', ['products' => $productRepository->findAll()]);
    }

    /**
     * @Route("/new", name="product_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $this->denyAccessUnlessGranted('new', $product);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setDateOfCreation(new \DateTime());
            $product->setDateOfLastModification(new \DateTime());
            $files = $form->get('image')->getData();
            foreach ($files as $file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $file->move($this->getParameter('image_directory'),
                    $fileName
                );
                $image = new Image();
                $image->setImage($fileName);
                $product->addImage($image);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();
            if ($product->getId() != null) {
                $this->addFlash('success', 'success');
            } else {
                $this->addFlash('error', 'error');
            }
            return $this->redirectToRoute('product_index');
        }
        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods="GET")
     */
    public function show(Product $product): Response
    {
        $this->denyAccessUnlessGranted('view', $product);
        return $this->render('product/show.html.twig', ['product' => $product]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods="GET|POST")
     */
    public function edit(Request $request, Product $product): Response
    {

        $this->denyAccessUnlessGranted('edit', $product);
        if ($request->get('item')) {
            $i = 0;
            foreach ($request->get('item') as $value) {
                $imageId = $value;
                $position = $i;
                $images = $product->getImages();
                foreach ($images as $image) {
                    if ($imageId == $image->getId())
                        $image->setPosition($position);
                }
                $i++;
            }
            $this->getDoctrine()->getManager()->flush();
        }
        if ($request->get('submit')) {
            $imageId = $request->get('image');
            $images = $product->getImages();
            foreach ($images as $image) {
                $image->setMain(false);
                if ($image->getId() == $imageId) {
                    $image->setMain(true);
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setDateOfLastModification(new \DateTime());
            $files = $form->get('image')->getData();
            foreach ($files as $file) {
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $file->move($this->getParameter('image_directory'),
                    $fileName
                );
                $image = new Image();
                $image->setImage($fileName);
                $product->addImage($image);
            }
            $this->getDoctrine()->getManager()->flush();
            if ($product->getId() != null) {
                $this->addFlash('success', 'success');
            } else {
                $this->addFlash('error', 'error');
            }
            return $this->redirectToRoute('product_edit', ['id' => $product->getId()]);
        }
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods="DELETE")
     */
    public function delete(Request $request, Product $product): Response
    {
        $this->denyAccessUnlessGranted('delete', $product);
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token')))
        {
            $em = $this->getDoctrine()->getManager();
            $em->remove($product);
            $em->flush();
            if ($product->getId() == null) {
                $this->addFlash('success', 'success');
            } else {
                $this->addFlash('error', 'error');
            }
        }
        return $this->redirectToRoute('product_index');
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        return md5(uniqid());
    }

}
