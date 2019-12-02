<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName',TextType::class, $this->getConfiguration('Prénom', 'Votre prénom ...'))
            ->add('lastName', TextType::class, $this->getConfiguration("Nom", "Votre nom ..."))
            ->add('email', EmailType::class, $this->getConfiguration("Email", "Votre email ..."))
            ->add('picture', UrlType::class, $this->getConfiguration("Avatar", "Votre image de profif"))
            ->add('introduction', TextType::class, $this->getConfiguration("Introduction", "Une courte présentation"))
            ->add('description', TextareaType::class, $this->getConfiguration("Description", "Présentez vous plus en détail"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
