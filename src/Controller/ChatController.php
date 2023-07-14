<?php

namespace App\Controller;

use DateTime;
use App\Entity\Channel;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChannelRepository;
use App\Repository\MessageRepository;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChatController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        return $this->render('chat/chat.html.twig');
    }

    #[IsGranted('ROLE_USER')]
    #[Route('channel/{id}', name: 'app_channel')]
    public function channel(Request $request, Channel $channel, HubInterface $hub, MessageRepository $messageRepository, ChannelRepository $channelRepository): Response
    {

        $messages = $messageRepository->findBy(['channel' => $channel->getId()], ['date' => 'DESC'], 5 );
        
        $messages = array_reverse($messages); 

        $form = $this->createForm(MessageType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            
            $NewMessage = $form->getData();
            
            $NewMessage->setdate(new DateTime())
            ->setAuthor($this->getUser())
            ->setChannel($channel)
            ;

            $messageRepository->save($NewMessage, 1);
            
            $update = new Update(
                'https://localhost:8000/channel/1',
                json_encode([
                    'message' => $NewMessage->getContent(),
                    'date' => $NewMessage->getdate()
                ]),
            );

            $hub->publish($update);


        }


        return $this->render('chat/channel.html.twig', [
            'form' => $form->createView(),
            'messages' => $messages,
            'channels' => $channelRepository->findAll()
        ]);
    }
}