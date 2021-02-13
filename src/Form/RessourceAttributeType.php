<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\RessourceAttribute;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RessourceAttributeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Selectionner la catégorie',
                'mapped' => 'false'
            ]);

        $builder
            ->get('category')->addEventListener(
                FormEvents::POST_SUBMIT,
                function (FormEvent $event) {
                    $form = $event->getForm();
                    $this->addCategoryAttribute($form->getParent(), $form->getData());
                }
            );



//        ------------------

        $builder
            ->add('value')
//            ->add('categoryAttribute')
//            ->add('ressources')
        ;

    }

    /**
     * Genere les champs attributs de la catégorie
     * @param FormInterface $form
     * @param Category $category
     */
    private function addCategoryAttribute(FormInterface $form, Category $category)
    {
        foreach($category->getCategoryAttributes() as $key => $value){

            $builder = $form->getConfig()->getFormFactory()->createNamedBuilder(
                'ressource_attribute'.$key,
                TextType::class,
                null,
                [
                    'mapped' => false,
                    'auto_initialize' => false,
                    'label' => $value->getName(),
                    'attr' => array(
                        'placeholder' => 'remplir l\'attribut',
                    )
                ]);

            $form->add($builder->getForm());

        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                if($form->getData() !== null){
                    dump($form->getData());
//                    dump(gettype($form->getData()));
//                    $this->addRestFormRessource($form->getParent(), $form->getData());
                }
            }
        );
        $form->add($builder->getForm());
        $form->add('description');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RessourceAttribute::class,
        ]);
    }

}
