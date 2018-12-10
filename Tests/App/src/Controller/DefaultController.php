<?php
namespace Kna\MoneyBundle\Tests\App\Controller;


use Money\Currency;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\Parser\AggregateMoneyParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    /**
     * @var AggregateMoneyParser
     */
    protected $parser;

    /**
     * @var AggregateMoneyFormatter
     */
    protected $formatter;

    public function __construct(
        AggregateMoneyParser $parser,
        AggregateMoneyFormatter $formatter
    )
    {
        $this->parser = $parser;
        $this->formatter = $formatter;
    }

    public function index(): Response
    {
        $data = [
            'money' => $this->parser->parse('$100'),
            'string' => $this->formatter->format(new Money(10000, new Currency('USD')))
        ];
        return new JsonResponse($data);
    }
}