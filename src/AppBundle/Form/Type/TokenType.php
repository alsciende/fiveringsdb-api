<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Token;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Alsciende <alsciende@icloud.com>
 */
class TokenType extends AbstractType
{
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class)
        ;
    }

    public function configureOptions (OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Token::class,
            'csrf_protection' => false,
        ]);
    }
}