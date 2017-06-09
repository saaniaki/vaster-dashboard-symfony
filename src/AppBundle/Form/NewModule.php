<?php

namespace AppBundle\Form;

use AppBundle\Entity\Module;
use AppBundle\Entity\ModuleInfo;
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewModule extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*
        * @var Page $page *
        $page = $options['page'];
        */

        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $options['entity_manager'];
        $allInfo = $em->getRepository("AppBundle:ModuleInfo")->findAll();

        /*

        $addedModules = $em->getRepository("AppBundle:Module")->findBy(['page' => $page]);

        $addedInfo = [];
        foreach( $addedModules as $module){
            /** @var ModuleInfo $info *
            $info = $module->getModuleInfo();
            array_push($addedInfo, $info->getId());
        }*/

        $choices = [];
        foreach( $allInfo as $info){
            //if( array_search($info->getId() ,$addedInfo) === false )
                $choices[$info->getName()] = $info;
        }


        $builder
            ->add("moduleInfo", ChoiceType::class, [
                'choices'  => $choices
                //'placeholder' => 'Choose an option',
            ])
            ->add("rank", IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Module::class
        ]);
        //['entity_manager', 'page']
        $resolver->setRequired('entity_manager');
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_new_module';
    }
}
