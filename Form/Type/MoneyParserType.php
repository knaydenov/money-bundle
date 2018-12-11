<?php
namespace Kna\MoneyBundle\Form\Type;


use Kna\MoneyBundle\Form\DataTransformer\MoneyToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MoneyParserType extends AbstractType
{
    /**
     * @var MoneyToStringTransformer
     */
    protected $transformer;

    public function __construct(MoneyToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return TextType::class;
    }
}