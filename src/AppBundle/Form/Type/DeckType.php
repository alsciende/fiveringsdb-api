<?php

declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Deck;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeckType extends AbstractType
{
    /** @var DataTransformerInterface */
    private $transformer;

    public function __construct (DataTransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name', TextType::class, [
                'required' => true,
            ]
            )
            ->add('description', TextType::class, [
                'empty_data' => '',
            ])
            ->add(
                'format', TextType::class, [
                'required' => true,
            ]
            )
            ->add(
                'cards', TextType::class, [
                'property_path' => 'deckCards',
            ]
            )
        ;

        $builder->get('cards')->addModelTransformer($this->transformer);
    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => Deck::class,
                'csrf_protection'    => false,
                'allow_extra_fields' => true,
            ]
        );
    }
}
