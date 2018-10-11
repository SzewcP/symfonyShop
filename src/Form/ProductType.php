<?php
namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('attr'=> array(
                'class'=>'form-control'
            )))
            ->add('description', TextareaType::class, array('attr'=> array(
                'class'=>'form-control'
            )))
            ->add('price')
            ->add('category', EntityType::class, array(
                'class'=> Category::class,
                'choice_label' => 'name',
                'multiple'=>true,
            ))
            ->add('image',FileType::class, array(
                'label'=>'image',
                'multiple'=>true,
                'data_class'=>null,
                'mapped'=>false,
                'required'=>false
                ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
