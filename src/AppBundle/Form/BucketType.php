<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BucketType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text',[
                    'label'  => 'Bucket Name',
                    'attr'   => [
                        'class'   => 'form-control',
                        'placeholder' => '#linux',
                        'type' => 'text'
                    ]
                ])
            ->add('owner')
        ;

        /*
              <div class="form-group">
                <label for="exampleInputEmail1">Email address</label>
                <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Email">
              </div>
         */
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Bucket'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_bucket';
    }
}
