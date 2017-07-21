<?php

declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Deck;
use AppBundle\Entity\DeckCard;
use AppBundle\Entity\Card;
use AppBundle\Form\DeckCardsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class DeckType extends AbstractType
{
  /** @var DataTransformerInterface */
  private $transformer;

  public function __construct(DataTransformerInterface $transformer)
  {
    $this->transformer = $transformer;
  }

    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('description', TextType::class)
            ->add('cards', TextType::class, [
              'property_path' => 'deckCards'
            ])
            ;

        $builder->get('cards')->addModelTransformer($this->transformer);
    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deck::class,
            'csrf_protection' => false,
        ]);
    }
}
