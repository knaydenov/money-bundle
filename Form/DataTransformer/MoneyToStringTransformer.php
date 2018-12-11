<?php
namespace Kna\MoneyBundle\Form\DataTransformer;


use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\Parser\AggregateMoneyParser;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class MoneyToStringTransformer implements DataTransformerInterface
{
    /**
     * @var AggregateMoneyFormatter
     */
    protected $formatter;
    /**
     * @var AggregateMoneyParser
     */
    protected $parser;

    public function __construct(AggregateMoneyFormatter $formatter, AggregateMoneyParser $parser)
    {
        $this->formatter = $formatter;
        $this->parser = $parser;
    }

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

        return $this->formatter->format($money);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        try {
            return $this->parser->parse($value);
        } catch (\InvalidArgumentException $exception) {
            throw new TransformationFailedException($exception->getMessage());
        }
    }
}