<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PinsController extends AbstractController
{


    /**
     * @Route("/", name="app_home", methods={"GET"})
     */
    public function index(EntityManagerInterface $em) : Response
    {
		$repo = $em->getRepository(Pin::class);

		$pins = $repo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('pins/index.html.twig', ['pins' => $pins]);
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET", "POST"})
     */
    public function create(Request $request, EntityManagerInterface $em)
    {

    	$pin = new Pin;

		$form = $this->createForm(PinType::class, $pin);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

			/*$data = $form->getData();

			$pin = new Pin;
    		$pin->setTitle($data['title']);
    		$pin->setDescription($data['description']);
*/
    		$em->persist($pin);
    		$em->flush();

    		return $this->redirectToRoute('app_pins_show', ['id'=>$pin->getId()]);

		}

    	return $this->render('pins/create.html.twig', [
    		'form' => $form->createView()
    	]);
    }


    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods={"GET"})
     */
    public function show(Pin $pin): Response
    {

    	/*$pin = $repo->find($id);*/

    	/*if ($pin === null) {
    		throw $this->createNotFoundException("Le Pin n°".$id." n'existe pas!");
    	}*/

    	return $this->render('pins/show.html.twig', compact('pin'));
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_delete", methods={"DELETE"})
     */
    public function delete(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {


        if ($this->isCsrfTokenValid('pin_deletion_'.$pin->getId(), $request->request->get('csrf_token'))) {

    		$em->remove($pin);
    		$em->flush();

        }

    		return $this->redirectToRoute('app_home');

    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET","PUT"})
     */
    public function edit(Request $request, EntityManagerInterface $em, Pin $pin): Response
    {
    
     	$form = $this->createForm(PinType::class, $pin, [
            'method' => 'PUT'
        ]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

    		$em->flush();

    		return $this->redirectToRoute('app_pins_show', ['id'=>$pin->getId()]);

		}

		return $this->render('pins/edit.html.twig', [
            'pin' => $pin,
    		'form' => $form->createView()
    	]);

    }

    /**
     * @Route("/pins/contact", name="app_pins_contact")
     */
    public function contact()
    {
    	return $this->render('pins/contact.html.twig');
    }

}
