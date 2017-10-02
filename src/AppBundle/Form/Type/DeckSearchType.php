<?php

namespace AppBundle\Form\Type;

use AppBundle\Search\DeckSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of DeckSearchType
 *
 * @author Alsciende <alsciende@icloud.com>
 */
class DeckSearchType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sort', ChoiceType::class, [
                'choices' => [
                    'recent' => 'recent',
                ],
            ])
            ->add('page', IntegerType::class);
    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'         => DeckSearch::class,
                'csrf_protection'    => false,
                'allow_extra_fields' => false,
            ]
        );
    }
}