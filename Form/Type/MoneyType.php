<?php
namespace Kna\MoneyBundle\Form\Type;


use Kna\MoneyBundle\Form\DataTransformer\MoneyToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MoneyType extends AbstractType
{
    /**
     * @var MoneyToArrayTransformer
     */
    protected $transformer;

    public function __construct(MoneyToArrayTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount', TextType::class)
            ->add('currency', CurrencyType::class)
        ;

        $builder->addModelTransformer($this->transformer);
    }

    public function getBlockPrefix()
    {
        return 'kna_money';
    }
}