<?php declare(strict_types=1);
/**
 * @author    Darvin Studio <info@darvin-studio.ru>
 * @copyright Copyright (c) 2018-2020, Darvin Studio
 * @link      https://www.darvin-studio.ru
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Darvin\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class Gateway redirect type
 */
class GatewayRedirectType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder Form Builder
     * @param array                $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event){
            $data = $event->getData();
            $form = $event->getForm();

            foreach ($form as $key => $child) {
                $form->remove($key);
            }

            foreach ($data as $key => $val) {
                $form->add($key, HiddenType::class);
            }

            $form->add('__payment_submit', SubmitType::class, [
                'label' => 'payment.form.gateway_redirect.payment_submit.label'
            ]);
        });
    }

    /**
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('action')
            ->setDefault('csrf_protection', false)
        ;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return '';
    }
}
