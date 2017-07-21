<?php

declare(strict_types=1);

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckCardsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        dump($builder);
//        dump($options);
        die;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }

    public function getParent()
    {
        return CollectionType::class;
    }
}
