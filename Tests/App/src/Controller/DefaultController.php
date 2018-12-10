<?php
namespace Kna\MoneyBundle\Tests\App\Controller;


use Kna\MoneyBundle\Form\Type\MoneyType;
use Money\Currency;
use Money\Formatter\AggregateMoneyFormatter;
use Money\Money;
use Money\Parser\AggregateMoneyParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

    public function index(Request $request): Response
    {
        $money = new Money(10000, new Currency('USD'));
        $form = $this
            ->createFormBuilder()
            ->add('balance', MoneyType::class)
            ->add('submit', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

        }

        $data = [
            'string' => $this->formatter->format($money),
            'money' => $this->parser->parse('$100'),
            'result' => $form->getData(),
            'form' => $form->createView()
        ];
        return $this->render('index.html.twig', $data);
    }
}