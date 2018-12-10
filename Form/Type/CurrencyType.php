<?php
namespace Kna\MoneyBundle\Form\Type;


use Kna\MoneyBundle\Form\DataTransformer\CurrencyToStringTransformer;
use Money\Currencies;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Money\Currency;

class CurrencyType extends AbstractType
{
    /**
     * @var Currencies
     */
    protected $currencies;
    /**
     * @var CurrencyToStringTransformer
     */
    protected $transformer;

    public function __construct(Currencies $currencies, CurrencyToStringTransformer $transformer)
    {
        $this->currencies = $currencies;
        $this->transformer = $transformer;
    }

    protected function resolveChoices(): \Generator
    {
        foreach ($this->currencies as /** @var Currency $currency */ $currency) {
            yield $currency->getCode() => $currency->getCode();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->resolveChoices()
        ]);
    }
}