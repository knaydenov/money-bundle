<?php
namespace Kna\MoneyBundle\Tests\App\Controller;


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

    public function __construct(AggregateMoneyParser $parser)
    {
        $this->parser = $parser;
    }

    public function index(): Response
    {
        $data = [
            'money' => $this->parser->parse('$100')
        ];
        return new JsonResponse($data);
    }
}