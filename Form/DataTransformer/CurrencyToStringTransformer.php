<?php
namespace Kna\MoneyBundle\Form\DataTransformer;


use Money\Currency;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class CurrencyToStringTransformer implements DataTransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transform($currency)
    {
        if (null === $currency) {
            return '';
        }

        if (!$currency instanceof Currency) {
            throw new UnexpectedTypeException($currency, Currency::class);
        }

        return $currency->getCode();
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($code)
    {
        if ($code === null || $code === '') {
            return null;
        }

        if (!is_string($code)) {
            throw new UnexpectedTypeException($code, 'string');
        }

        try {
            $currency = new Currency($code);
        } catch (\InvalidArgumentException $exception) {
            throw new TransformationFailedException($exception->getMessage());
        }

        return $currency;
    }
}