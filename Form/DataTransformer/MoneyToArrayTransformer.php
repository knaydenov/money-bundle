<?php
namespace Kna\MoneyBundle\Form\DataTransformer;


use Money\Money;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class MoneyToArrayTransformer implements DataTransformerInterface
{

    /**
     * {@inheritdoc}
     */
    public function transform($money)
    {
        if (null === $money) {
            return null;
        }

        if (!$money instanceof Money) {
            throw new UnexpectedTypeException($money, Money::class);
        }

        return ['amount' => $money->getAmount(), 'currency' => $money->getCurrency()];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($data)
    {
        if ($data === null || $data === '') {
            return null;
        }

        if (!is_array($data)) {
            throw new UnexpectedTypeException($data, 'array');
        }

        try {
            if (!($data['currency'] ?? null)) {
                throw new \InvalidArgumentException('Invalid currency');
            }
            $money = new Money($data['amount'], $data['currency']);
        } catch (\InvalidArgumentException $exception) {
            throw new TransformationFailedException($exception->getMessage());
        }

        return $money;
    }
}