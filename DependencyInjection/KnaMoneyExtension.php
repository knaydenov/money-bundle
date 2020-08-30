<?php
namespace Kna\MoneyBundle\DependencyInjection;


use Money\Currencies\AggregateCurrencies;
use Money\Currencies\BitcoinCurrencies;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\BitcoinMoneyFormatter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;
use Money\Parser\BitcoinMoneyParser;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlMoneyParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class KnaMoneyExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('forms.xml');

        $this->configureCurrencies($container, $config);
        $this->configureParser($container, $config);
        $this->configureFormatter($container, $config);
    }

    protected function configureCurrencies(ContainerBuilder $container, array $config)
    {
        $bitcoinCurrencies = new Definition(BitcoinCurrencies::class);
        $container->setDefinition('kna_money.currencies.bitcoin', $bitcoinCurrencies);
        $container->setAlias(BitcoinCurrencies::class, 'kna_money.currencies.bitcoin');

        $isoCurrencies = new Definition(ISOCurrencies::class);
        $container->setDefinition('kna_money.currencies.iso', $isoCurrencies);
        $container->setAlias(BitcoinCurrencies::class, 'kna_money.currencies.iso');

        $aggregateCurrencies = new Definition(AggregateCurrencies::class, [[$bitcoinCurrencies, $isoCurrencies]]);

        $container->setDefinition('kna_money.currencies.aggregate', $aggregateCurrencies);
        $container->setAlias(AggregateCurrencies::class, 'kna_money.currencies.aggregate');
    }

    protected function configureParser(ContainerBuilder $container, array $config)
    {
        $decimalParser = new Definition(DecimalMoneyParser::class, [$container->getDefinition('kna_money.currencies.aggregate')]);
        $decimalParser->addTag('kna_money.parser');
        $decimalParser->setPublic(true);
        $container->setDefinition('kna_money.parser.decimal', $decimalParser);
        $container->setAlias(DecimalMoneyParser::class, 'kna_money.parser.decimal');

        $bitcoinParser = new Definition(BitcoinMoneyParser::class, [$config['bitcoin']['fraction_digits']]);
        $bitcoinParser->addTag('kna_money.parser');
        $bitcoinParser->setPublic(true);
        $container->setDefinition('kna_money.parser.bitcoin', $bitcoinParser);
        $container->setAlias(BitcoinMoneyParser::class, 'kna_money.parser.bitcoin');

        if (extension_loaded('intl')) {
            $numberFormatter = new Definition(\NumberFormatter::class, [$config['locale'], \NumberFormatter::CURRENCY]);
            $intlParser = new Definition(IntlMoneyParser::class, [$numberFormatter, $container->getDefinition('kna_money.currencies.aggregate')]);
            $intlParser->setPublic(true);
            $intlParser->addTag('kna_money.parser');
            $container->setDefinition('kna_money.parser.intl', $intlParser);
            $container->setAlias(IntlMoneyParser::class, 'kna_money.parser.intl');
        }

        $parser = $container->findDefinition('kna_money.parser.aggregate');
        $taggedParsers = $container->findTaggedServiceIds('kna_money.parser');
        $parserReferences = array_map(function ($id) { return new Reference($id);}, array_keys($taggedParsers));

        $parser->setArgument(0, $parserReferences);
    }

    protected function configureFormatter(ContainerBuilder $container, array $config)
    {
        $isoCurrencyCodes = array_map(function (Currency $currency) {
            return $currency->getCode();
        }, iterator_to_array(new ISOCurrencies()));

        $decimalFormatter = new Definition(DecimalMoneyFormatter::class, [$container->getDefinition('kna_money.currencies.aggregate')]);
        $decimalFormatter->setPublic(true);
        foreach ($isoCurrencyCodes as $isoCurrencyCode) {
            $decimalFormatter->addTag('kna_money.formatter', ['currency' => $isoCurrencyCode]);
        }
        $container->setDefinition('kna_money.formatter.decimal', $decimalFormatter);
        $container->setAlias(DecimalMoneyFormatter::class, 'kna_money.formatter.decimal');

        $bitcoinFormatter = new Definition(BitcoinMoneyFormatter::class, [$config['bitcoin']['fraction_digits'], $container->getDefinition('kna_money.currencies.aggregate')]);
        $bitcoinFormatter->setPublic(true);
        $bitcoinFormatter->addTag('kna_money.formatter', ['currency' => BitcoinCurrencies::CODE]);
        $container->setDefinition('kna_money.formatter.bitcoin', $bitcoinFormatter);
        $container->setAlias(BitcoinMoneyFormatter::class, 'kna_money.formatter.bitcoin');

        if (extension_loaded('intl')) {
            $decimalNumberFormatter = new Definition(\NumberFormatter::class, [$config['locale'], \NumberFormatter::DECIMAL]);
            $intlDecimalFormatter = new Definition(IntlMoneyFormatter::class, [$decimalNumberFormatter, $container->getDefinition('kna_money.currencies.aggregate')]);
            $intlDecimalFormatter->setPublic(true);
            foreach ($isoCurrencyCodes as $isoCurrencyCode) {
                $intlDecimalFormatter->addTag('kna_money.formatter', ['currency' => $isoCurrencyCode]);
            }
            $container->setDefinition('kna_money.formatter.intl_decimal', $intlDecimalFormatter);

            $currencyNumberFormatter = new Definition(\NumberFormatter::class, [$config['locale'], \NumberFormatter::CURRENCY]);
            $intlMoneyFormatter = new Definition(IntlMoneyFormatter::class, [$currencyNumberFormatter, $container->getDefinition('kna_money.currencies.aggregate')]);
            $intlMoneyFormatter->setPublic(true);
            foreach ($isoCurrencyCodes as $isoCurrencyCode) {
                $intlMoneyFormatter->addTag('kna_money.formatter', ['currency' => $isoCurrencyCode]);
            }
            $container->setDefinition('kna_money.formatter.intl_money', $intlMoneyFormatter);
            $container->setAlias(IntlMoneyFormatter::class, 'kna_money.formatter.intl_money');
        }

        $formatter = $container->findDefinition('kna_money.formatter.aggregate');
        $taggedFormatters = $container->findTaggedServiceIds('kna_money.formatter');

        $formatterConfigs = [];

        foreach ($taggedFormatters as $id => $tags) {
            $formatterReference = new Reference($id);
            foreach ($tags as $tag) {
                if (isset($tag['currency'])) {
                    $formatterConfigs[] = [
                        'priority' => $tag['priority'] ?? 0,
                        'currency' => $tag['currency'],
                        'formatter' => $formatterReference,
                    ];
                }
            }
        }

        usort($formatterConfigs, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return 0;
            }
            return ($a['priority'] < $b['priority']) ? -1 : 1;
        });

        $formatters = [];
        foreach ($formatterConfigs as $formatterConfig) {
            $formatters[$formatterConfig['currency']] = $formatterConfig['formatter'];
        }

        $formatter->setArgument(0, $formatters);
    }

}