<?php

namespace App\Twig\Components;

use App\Dto\ContactDto;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class ContactMessage extends AbstractController
{
    use DefaultActionTrait;
    use ComponentWithFormTrait;

    #[LiveProp]
    public ?ContactDto $initialFormData = null;

    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly EntityManagerInterface $em,
    ){
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(ContactFormType::class, $this->initialFormData);
    }

    #[LiveAction]
    public function save(): void
    {
        $this->submitForm();
        if (!$this->getForm()->isValid()) return;

        $this->dispatchBrowserEvent();
        $this->resetForm();
    }

    private function dispatchBrowserEvent(): void
    {
        $form = $this->getForm();

        $this->mailer->send( (new Email())
            ->from(new Address($form->get('email')->getData(), $form->get('name')->getData()))
            ->to('conatct@symfony.com')
            ->subject($form->get('subject')->getData())
            ->text($form->get('message')->getData())
        );
    }
}
