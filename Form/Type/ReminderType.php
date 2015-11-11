<?php

namespace Flower\PlannerBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Flower\ModelBundle\Entity\Planner\Reminder;
class ReminderType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('type', 'choice', array(
                        'choices' => array('1' => 'e-mail')))
                ->add('unity', 'choice', array(
                        'choices' => array(Reminder::$UNITY_MINUTES => 'minutes'
                                        ,Reminder::$UNITY_HOUR => 'hours'
                                        ,Reminder::$UNITY_DAY => 'days'
                                        ,Reminder::$UNITY_WEEK => 'weeks')))
                ->add('amount')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Flower\ModelBundle\Entity\Planner\Reminder',
            'translation_domain' => 'Reminder',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'reminder';
    }

}
