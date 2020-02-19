<?php


namespace App\Service;


use App\Entity\Event;
use App\Entity\Status;
use DateInterval;
use Doctrine\ORM\EntityManagerInterface;

class UpdateEventStatus
{

    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function updateEventStatus($id){
        $event = $this->em->getRepository(Event::class)->find($id);

        //The event have created status ?
        $createdEvent = $event->getStatus()->getLibel() === Status::created();
        //The event have opened status ?
        $openedEvent = $event->getStatus()->getLibel() === Status::opened();
        //The event have ongoing status ?
        $ongoingEvent = $event->getStatus()->getLibel() === Status::ongoing();
        //The event have closed status ?
        $closedEvent = $event->getStatus()->getLibel() === Status::closed();
        //The event have finished status ?
        $finishedEvent = $event->getStatus()->getLibel() === Status::finished();
        //The event have cancelled status ?
        $cancelledEvent = $event->getStatus()->getLibel() === Status::cancelled();


        //l'evt est-il closed ? (passage du statut opened à closed)
        $nowEventIsClosed = $event->getInscriptionDeadLine() < new \DateTime('now');
        //l'evnt est-il commencé ? (pour passage statut opened à ongoing)
        $nowEventIsStarted = $event->getStartingDateTime() < new \DateTime('now');

        //duration
        $duration = $event->getDuration();

        //l'evnt est-il terminé ? (passage du statut ongoing à finished)
        try {
            $nowEventIsFinished = ($event->getStartingDateTime()->add(new DateInterval('P' . $duration . 'D')) < new \DateTime('now'));
        } catch (\Exception $e) {
        }
        //l'evnt est-il archivé ? (passage du statut fininshed à archived)
        try {
            $nowEventIsArchived = $event->getStartingDateTime()->add(new DateInterval('P' . ($duration+30) . 'D'));
        } catch (\Exception $e) {
        }



        //Passer du statut opened à closed
//        if ($openedEvent && $nowEventIsClosed) {
//            $statusClosed = $this->em->getRepository(Status::class)->findByLibel(Status::closed());
//            $event->setStatus($statusClosed);
//        }

        //Passer du statut opened à ongoing quand l'évennement commence
        if ($openedEvent && $nowEventIsStarted){
            $statusOnGoing = $this->em->getRepository(Status::class)->findByLibel(Status::ongoing());
            $event->setStatus($statusOnGoing);
        }

//        //Passer du statut closed à ongoing
//        if($closedEvent && $nowEventIsStarted){
//            $statusOnGoing = $this->em->getRepository(Status::class)->findByLibel(Status::ongoing());
//            $event->setStatus($statusOnGoing);
//        }

        //Passer du statut ongoing à finished
        if($ongoingEvent && $nowEventIsFinished) {
            $statusFinished = $this->em->getRepository(Status::class)->findByLibel(Status::finished());
            $event->setStatus($statusFinished);
        }

        //Passer du statut finished à archived
        //TODO en cas de d'évennement cancelled il faut les archiver après 30 jours aussi
        if($finishedEvent && $nowEventIsArchived) {
            $statusArchived = $this->em->getRepository(Status::class)->findByLibel(Status::archived());
            $event->setStatus($statusArchived);

        }


    //TODO : persist et flush à la fin

    }

}