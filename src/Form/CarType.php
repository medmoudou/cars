<?php

namespace App\Form;

use App\Entity\Car;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CarType extends AbstractType
{

    public function __construct(public CategoryRepository $categoryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('cost')
            ->add('nbSeats')
            ->add('nbDoors')
            ->add('category', ChoiceType::class, array(
                'choices'  => $this->categoryRepository->findAll(),
                'choice_label' => 'name',
                'choice_value' => function (?Category $category) {
                    return $category ? $category->getId() : '';
                },
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Car::class,
        ]);
    }
}
