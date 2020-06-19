<?php

namespace App\Controller;

use App\Entity\Pin;
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
    	/*if($request->isMethod('POST'))
    	{
    		$data = $request->request->all();

    		if ($this->isCsrfTokenValid('pins_create', $data['_token']))
    		{

    		$pin = new Pin;
    		$pin->setTitle($data['title']);
    		$pin->setDescription($data['description']);

    		$em->persist($pin);
    		$em->flush();


    		}

    		return $this->redirectToRoute('app_home');

    	}*/

    	$pin = new Pin;

		$form = $this->createFormBuilder($pin)
			->add('title', TextType::class, ['label' => 'Titre','attr' => ['autofocus' => 'autofocus']])
			->add('description', TextareaType::class, ['attr' => ['rows' => 10, 'cols' => 60]])
/*			->add('submit', SubmitType::class, ['label' => 'Create pin'])
*/			->getForm()
		;

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
    		'formulaireCréation' => $form->createView()
    	]);
    }


    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show")
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
     * @Route("/pins/delete/{id<[0-9]+>}", name="app_pins_delete")
     */
    public function delete(Request $request, EntityManagerInterface $em, $id): Response
    {
    	if ($request -> isMethod('GET')) {
    		$repo = $em->getRepository(Pin::class);
    		$pin = $repo->find($id);

    		$em->remove($pin);
    		$em->flush();

    		return $this->redirectToRoute('app_home');

    	}

    }

    /**
     * @Route("/pins/edit/{id<[0-9]+>}", name="app_pins_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, $id): Response
    {
    	$repo = $em->getRepository(Pin::class);
    	$pin = $repo->find($id);

    	/*dd($pin);*/

    	$form = $this->createFormBuilder($pin)
			->add('title', TextType::class, ['label' => 'Titre','attr' => ['autofocus' => 'autofocus']])
			->add('description', TextareaType::class, ['attr' => ['rows' => 10, 'cols' => 60]])
			->getForm()
		;

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {

    		$em->persist($pin);
    		$em->flush();

    		return $this->redirectToRoute('app_pins_show', ['id'=>$pin->getId()]);

		}

		return $this->render('pins/edit.html.twig', [
    		'formulaireEdition' => $form->createView()
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
