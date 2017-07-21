<?php

declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Form\DeckCardsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class DeckType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
//            ->add('cards', DeckCardsType::class)
            ->add('deckCards', TextType::class)
            ;

        $builder->get('deckCards')
          ->addModelTransformer(new CallbackTransformer(
            function ($cardsToOutput) {
                return null;
            },
            function ($cardsFromInput) {
              dump($cardsFromInput);die;
            }
          ));
    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deck::class,
            'csrf_protection' => false,
        ]);
    }
}
