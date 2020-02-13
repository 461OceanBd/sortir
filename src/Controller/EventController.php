<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventState;
use App\EventState\EventStateHelper;
use App\Form\EventSearchType;
use App\Form\EventType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sorties", name="event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("", name="list")
     */
    public function list(Request $request)
    {
        //valeurs par défaut du formulaire de recherche
        $searchData = ['subscribed_to' => true, 'not_subscribed_to' => true];
        $searchForm = $this->createForm(EventSearchType::class, $searchData);
        $searchForm->handleRequest($request);
        $searchData = $searchForm->getData();

        $eventRepo = $this->getDoctrine()->getRepository(Event::class);
        $events = $eventRepo->search($this->getUser(), $searchData);

        return $this->render('event/list.html.twig', [
            'events' => $events,
            'searchForm' => $searchForm->createView()
        ]);
    }

    /**
     * @Route("/ajout", name="create")
     */
    public function create(Request $request)
    {
        $event = new Event();
        $eventForm = $this->createForm(EventType::class, $event);
        $eventStateRepo = $this->getDoctrine()->getRepository(EventState::class);

        $eventForm->handleRequest($request);

        if ($eventForm->isSubmitted() && $eventForm->isValid()){
            //on donne l'état "créée" à cette sortie
            $createdState = $eventStateRepo->findOneBy(['name' => 'created']);
            $event->setState($createdState);

            //on renseigne son auteur (le user actuel)
            $event->setAuthor($this->getUser());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($event);
            $manager->flush();

            $this->addFlash('success', 'Sortie créée, bravo !');
            return $this->redirectToRoute('event_list');
        }

        return $this->render('event/create.html.twig', [
            'eventForm' => $eventForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/publier", name="publish")
     */
    public function publish(Event $event, EventStateHelper $stateHelper)
    {
        $stateHelper->changeEventState($event, "open");
        return $this->redirectToRoute('event_list');
    }


    /**
     * @Route("/{id}/annuler", name="cancel")
     */
    public function cancel(Event $event, EventStateHelper $stateHelper)
    {
        $stateHelper->changeEventState($event, "canceled");
        return $this->redirectToRoute('event_list');
    }
}
