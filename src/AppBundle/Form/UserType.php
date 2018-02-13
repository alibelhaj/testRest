<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom' ,TextType::class, [
                'label' => false,
                'required' => true,

            ])
            ->add('prenom' ,TextType::class, [
                'label' => false,
                'required' => true,

            ])
            ->add('email' ,EmailType::class, [
                'label' => false,
                'required' => true,

            ])
            ->add('actif' ,CheckboxType::class, [
                'label' => false,
                'required' => false,

            ])
            ->add('groupe' ,EntityType::class,  array(
                'class' => 'AppBundle:Groupe',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
            'csrf_protection' => false,
            'allow_extra_fields' => true,



        ));
    }

    public function getName()
    {
        return 'user';
    }
}
